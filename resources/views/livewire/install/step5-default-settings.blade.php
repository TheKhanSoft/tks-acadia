<?php

namespace App\Volt\Installation;

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.installation')] class extends Component {
    public string $siteTitle = '';
    public string $adminEmail = '';
    public string $message = '';

    public function saveSettings()
    {
        // TODO: Implement logic to save settings (e.g., to config file or database)
        // Example: Config::set('app.name', $this->siteTitle);
        // Example: Setting::updateOrCreate(['key' => 'admin_email'], ['value' => $this->adminEmail]);

        $this->message = 'Default settings saved successfully!';
        // TODO: Add logic to complete installation or proceed to login (e.g., redirect to login page)
        // return redirect()->route('login'); // Example redirect
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $this->step = 5; // Set the current step to 5
        return view('livewire.install.step5-default-settings');
    }
};
?>

<div
    style="font-family: 'Roboto', sans-serif; max-width: 500px; margin: 40px auto; padding: 30px; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 12px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);">
    <h2 style="text-align: center; color: #333; font-size: 24px;">Default Settings</h2>

    @if ($message)
        <div
            style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 12px; margin-bottom: 25px; border-radius: 6px; text-align: center; font-size: 16px;">
            {{ $message }}</div>
    @endif

    <form wire:submit.prevent="saveSettings">
        <div style="margin-bottom: 20px;">
            <label for="siteTitle"
                style="display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 15px;">Site
                Title:</label>
            <input type="text" id="siteTitle" wire:model="siteTitle"
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="adminEmail"
                style="display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 15px;">Admin Email
                for Notifications:</label>
            <input type="email" id="adminEmail" wire:model="adminEmail"
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
        </div>

        {{-- Add more settings fields here --}}

        <button type="submit"
            style="display: block; width: 100%; padding: 14px; background-color: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 18px; font-weight: bold; transition: background-color 0.3s ease; text-transform: uppercase;">Save
            Settings</button>
    </form>

    <div style="margin-top: 30px; text-align: center;">
        <a href="{{ route('install.step4-admin-details') }}"
            style="text-decoration: none; color: #007bff; font-size: 15px; display: flex; align-items: center; justify-content: center; transition: color 0.3s ease;">
            &larr; <span style="margin-left: 5px;">Previous Step (Admin Details)</span>
        </a>
    </div>
</div>
