<?php

namespace App\Volt\Installation;

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.installation')] class extends Component {
    use Toast;

    public array $availableSeeders = [];
    public array $selectedSeeders = [];
    public string $output = '';
    public bool $seedingComplete = false;
    public bool $showOutput = false;

    public function mount()
    {
        $this->availableSeeders = $this->getAvailableSeeders();
    }

    public function toggleSeeder($seederName)
    {
        if (in_array($seederName, $this->selectedSeeders)) {
            $this->selectedSeeders = array_diff($this->selectedSeeders, [$seederName]);
        } else {
            $this->selectedSeeders[] = $seederName;
        }
    }

    public function getAvailableSeeders(): array
    {
        $seeders = [];
        $seederPath = database_path('seeders');
        $files = File::files($seederPath);

        foreach ($files as $file) {
            $fileName = $file->getBasename('.php');
            if ($fileName !== 'DatabaseSeeder') {
                // Convert CamelCase/PascalCase to spaced string and remove "Seeder" suffix
                $displayName = preg_replace('/(?<!^)[A-Z]/', ' $0', $fileName);
                $displayName = str_replace('Seeder', '', $displayName);
                $displayName = trim($displayName); // Remove leading/trailing spaces
                $seeders[$fileName] = $displayName; // Store original filename as key and display name as value
            }
        }

        return $seeders;
    }

    public function skipStep()
    {
        return redirect()->route('install.step4-admin-details'); // Assuming route names based on paths
    }

    public function runSelectedSeeders()
    {
        $this->output = '';
        $this->seedingComplete = false;
        $this->showOutput = true;

        if (empty($this->selectedSeeders)) {
            $this->output .= 'You have not selected any default data to be migrated/saved to the database. Kindly select';
            $this->warning('No Default Selection', 'You have not selected any default data to be migrated/saved to the database. Kindly select');
            return;
        }
        // dd($this->selectedSeeders);
        try {
            foreach ($this->selectedSeeders as $seeder) {
                Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
                $this->output .= "Output for {$seeder}:\n" . Artisan::output() . "\n\n";
            }
            $this->seedingComplete = true;
            return redirect()->route('install.step4-admin-details'); // Redirect to Step 4
        } catch (\Exception $e) {
            $this->output .= 'Error running seeders: ' . $e->getMessage();
            $this->error('Error', 'Error running seeders: ' . $e->getMessage());
            $this->seedingComplete = false;
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $this->step = 3; // Set the current step to 3
        return view('livewire.install.step3-seed');
    }
};

?>

<div
    style="font-family: 'Roboto', sans-serif; max-width: 600px; margin: 40px auto; padding: 30px; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 12px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);">
    <h2 style="text-align: center; color: #333; font-size: 24px;">Database Seeding</h2>

    @if (!$seedingComplete)
        <p style="text-align: center; color: #555; margin-bottom: 25px; font-size: 16px;">Select the seeders you want to
            run:</p>
        <div style="margin-bottom: 25px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #fefefe; max-height: 250px; overflow-y: auto; display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
            @forelse ($availableSeeders as $fileName => $displayName)
                <label style="display: flex; align-items: center; cursor: pointer; color: #333; font-size: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color: {{ in_array($fileName, $selectedSeeders) ? '#d4edda' : '#eee' }}; transition: background-color 0.3s ease;"
                       wire:click="toggleSeeder('{{ $fileName }}')">
                    <input type="checkbox" wire:model="selectedSeeders" value="{{ $fileName }}" style="display: none;">
                    <span style="flex-grow: 1;">{{ $displayName }}</span>
                    <span style="width: 20px; height: 20px; border-radius: 50%; background-color: {{ in_array($fileName, $selectedSeeders) ? '#28a745' : '#ccc' }}; display: inline-block; margin-left: 10px; position: relative;">
                         @if(in_array($fileName, $selectedSeeders))
                            <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 12px;">&#10003;</span>
                        @endif
                    </span>
                </label>
            @empty
                <p style="text-align: center; color: #777; font-style: italic; grid-column: span 2;">No seeders found in the database/seeders directory (excluding DatabaseSeeder.php).</p>
            @endforelse
        </div>

        <button wire:click="runSelectedSeeders"
            style="display: block; width: 100%; padding: 14px; background-color: #28a745; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 18px; font-weight: bold; transition: background-color 0.3s ease; text-transform: uppercase;">Run
            Selected Seeders</button>
    @endif
    @if ($showOutput)
        <h3 style="margin-top: 30px; text-align: center; color: #333; font-size: 20px;">Seeding Output:</h3>
        <pre
            style="background-color: #e9ecef; padding: 20px; border-radius: 8px; white-space: pre-wrap; word-wrap: break-word; max-height: 350px; overflow-y: auto; color: #333; border: 1px solid #ced4da; line-height: 1.5;">{{ $output }}</pre>
        {{-- TODO: Add button to proceed to Step 4 --}}
    @endif

    <div style="margin-top: 30px; text-align: center; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('install.step2-migrate') }}" style="text-decoration: none; color: #007bff; font-size: 15px; display: flex; align-items: center; transition: color 0.3s ease;">
            &larr; <span style="margin-left: 5px;">Previous Step (Migrations)</span>
        </a>
        <a href="{{ route('install.step4-admin-details') }}" style="text-decoration: none; color: #007bff; font-size: 15px; display: flex; align-items: center; transition: color 0.3s ease;">
            Skip Step (Admin Details) <span style="margin-left: 5px;">&rarr;</span>
        </a>
    </div>
</div>
