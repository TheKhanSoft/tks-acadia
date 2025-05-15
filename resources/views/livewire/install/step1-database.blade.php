<?php

namespace App\Volt\Installation;

use Livewire\Volt\Component;
use Illuminate\Support\Facades\File;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.installation')] class extends Component {
    use Toast;

    public string $dbHost = '127.0.0.1';
    public string $dbPort = '3306';
    public string $dbDatabase = '';
    public string $dbUsername = '';
    public string $dbPassword = '';
    public int $delay = 3000; // Default delay in milliseconds
    public int $step = 1;

    public function mount()
    {
        $this->dbHost = env('DB_HOST', '127.0.0.1');
        $this->dbPort = env('DB_PORT', '3306');
        $this->dbDatabase = env('DB_DATABASE', '');
        $this->dbUsername = env('DB_USERNAME', '');
        $this->dbPassword = env('DB_PASSWORD', '');
    }

    public function skipStep()
    {
        return redirect()->route('install.step2-migrate');
        // $this->dispatch('redirect-with-delay', url: route('install.step2-migrate'), delay: $this->delay);
    }

    public function saveDatabaseSettings()
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        $replacements = [
            'DB_HOST=' => 'DB_HOST=' . $this->dbHost,
            'DB_PORT=' => 'DB_PORT=' . $this->dbPort,
            'DB_DATABASE=' => 'DB_DATABASE=' . $this->dbDatabase,
            'DB_USERNAME=' => 'DB_USERNAME=' . $this->dbUsername,
            'DB_PASSWORD=' => 'DB_PASSWORD=' . $this->dbPassword,
        ];

        foreach ($replacements as $key => $value) {
            $envContent = preg_replace('/^' . preg_quote($key, '/') . '.*$/m', $value, $envContent);
        }

        File::put($envPath, $envContent);

        $this->success('Database Settings Updated', 'Your database connection details have been saved. Redirecting to the next step in a few moments.');

        // Use JavaScript to redirect after a delay
        $this->dispatch('redirect-with-delay', url: route('install.step2-migrate'), delay: 5000);
        $this->step++;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $this->step = 1; // Set the current step to 1
        return view('livewire.install.step1-database');
    }
};

?>

<div
    style="font-family: 'Roboto', sans-serif; max-width: 500px; margin: 40px auto; padding: 30px; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 12px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);">
    <h2 style="text-align: center; color: #333; font-size: 24px;">Database Configuration</h2>

    @if (session('message'))
        <div
            style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 12px; margin-bottom: 25px; border-radius: 6px; text-align: center; font-size: 16px;">
            {{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="saveDatabaseSettings">
        <div style="margin-bottom: 20px;">
            <label for="dbHost"
                style="display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 15px;">Database
                Host:</label>
            <input type="text" id="dbHost" wire:model="dbHost"
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="dbPort"
                style="display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 15px;">Database
                Port:</label>
            <input type="text" id="dbPort" wire:model="dbPort"
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="dbDatabase"
                style="display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 15px;">Database
                Name:</label>
            <input type="text" id="dbDatabase" wire:model="dbDatabase"
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="dbUsername"
                style="display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 15px;">Database
                Username:</label>
            <input type="text" id="dbUsername" wire:model="dbUsername"
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="dbPassword"
                style="display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 15px;">Database
                Password:</label>
            <input type="password" id="dbPassword" wire:model="dbPassword"
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
        </div>

        <button type="submit"
            style="display: block; width: 100%; padding: 14px; background-color: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 18px; font-weight: bold; transition: background-color 0.3s ease; text-transform: uppercase;">Save
            Settings</button>
    </form>

    <div style="margin-top: 20px; text-align: center;">
        <a href="{{ route('install.step2-migrate') }}"
            style="text-decoration: none; color: #007bff; font-size: 15px; display: flex; align-items: center; justify-content: center; transition: color 0.3s ease;">
            Skip Step (Admin Details) <span style="margin-left: 5px;">&rarr;</span>
        </a>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('redirect-with-delay', ({url, delay }) => {
                setTimeout(() => {
                    window.location.href = url;
                }, delay);
            });
        });
    </script>
</div>
