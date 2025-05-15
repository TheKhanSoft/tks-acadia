<?php

namespace App\Volt\Installation;

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Artisan;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.installation')] class extends Component {
    use Toast;

    public string $output = '';
    public bool $migrationsRun = false;

    public function skipStep()
    {
        return redirect()->route('install.step3-seed'); // Assuming route names based on paths
    }

    public function runMigrations()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $this->output = Artisan::output();
            $this->migrationsRun = true;
            return redirect()->route('install.step3-seed'); // Redirect to Step 3
        } catch (\Exception $e) {
            $this->output = 'Error running migrations: ' . $e->getMessage();
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $this->step = 2; // Set the current step to 2
        return view('livewire.install.step2-migrate');
    }
};

?>

<div
    style="font-family: 'Roboto', sans-serif; max-width: 600px; margin: 40px auto; padding: 30px; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 12px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);">
    <h2 style="text-align: center; color: #333; font-size: 24px;">Database Migrations</h2>

    @if (!$migrationsRun)
        <p style="text-align: center; color: #555; margin-bottom: 25px; font-size: 16px;">Click the button below to run
            the database migrations.</p>
        <button wire:click="runMigrations"
            style="display: block; width: 100%; padding: 14px; background-color: #007bff; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 18px; font-weight: bold; transition: background-color 0.3s ease; text-transform: uppercase;">Run
            Migrations</button>
    @else
        <h3 style="margin-top: 30px; text-align: center; color: #333; font-size: 20px;">Migration Output:</h3>
        <pre
            style="background-color: #e9ecef; padding: 20px; border-radius: 8px; white-space: pre-wrap; word-wrap: break-word; max-height: 350px; overflow-y: auto; color: #333; border: 1px solid #ced4da; line-height: 1.5;">{{ $output }}</pre>
        {{-- TODO: Add button to proceed to Step 3 --}}
    @endif

    <div
        style="margin-top: 30px; text-align: center; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('install.step1-database') }}"
            style="text-decoration: none; color: #007bff; font-size: 15px; display: flex; align-items: center; transition: color 0.3s ease;">
            &larr; <span style="margin-left: 5px;">Previous Step (Database)</span>
        </a>
        <a href="{{ route('install.step3-seed') }}"
            style="text-decoration: none; color: #007bff; font-size: 15px; display: flex; align-items: center; transition: color 0.3s ease;">
            Skip Step (Seeding) <span style="margin-left: 5px;">&rarr;</span>
        </a>
    </div>
</div>
