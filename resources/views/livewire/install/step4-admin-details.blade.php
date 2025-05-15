<?php

namespace App\Volt\Installation;

use Livewire\Volt\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.installation')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirmation = '';
    public string $message = '';

    public function skipStep()
    {
        return redirect()->route('install.step5-default-settings'); // Assuming route names based on paths
    }

    public function createAdminUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                // TODO: Assign admin role or flag if applicable
            ]);

            $this->message = 'Admin user created successfully!';
            return redirect()->route('install.step5-default-settings'); // Redirect to Step 5
        } catch (\Exception $e) {
            $this->message = 'Error creating admin user: ' . $e->getMessage();
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $this->step = 4; // Set the current step to 4
        return view('livewire.install.step4-admin-details');
    }
};

?>

<div
    style="font-family: 'Roboto', sans-serif; max-width: 500px; margin: 40px auto; padding: 30px; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 12px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);">
    <h2 style="text-align: center; color: #333; font-size: 24px;">Admin User Details</h2>

    @if ($message)
        <div
            style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 12px; margin-bottom: 25px; border-radius: 6px; text-align: center; font-size: 16px;">
            {{ $message }}</div>
    @endif

    <form wire:submit.prevent="createAdminUser">
        <div style="margin-bottom: 20px;">
            <label for="name"
                style="display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 15px;">Name:</label>
            <input type="text" id="name" wire:model="name"
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
            @error('name')
                <span style="color: #dc3545; font-size: 0.9em;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="email"
                style="display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 15px;">Email:</label>
            <input type="email" id="email" wire:model="email"
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
            @error('email')
                <span style="color: #dc3545; font-size: 0.9em;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="password"
                style="display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 15px;">Password:</label>
            <input type="password" id="password" wire:model="password"
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
            @error('password')
                <span style="color: #dc3545; font-size: 0.9em;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="passwordConfirmation"
                style="display: block; margin-bottom: 8px; font-weight: bold; color: #555; font-size: 15px;">Confirm
                Password:</label>
            <input type="password" id="passwordConfirmation" wire:model="passwordConfirmation"
                style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
        </div>

        <button type="submit"
            style="display: block; width: 100%; padding: 14px; background-color: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 18px; font-weight: bold; transition: background-color 0.3s ease; text-transform: uppercase;">Create
            Admin User</button>
    </form>

    <div
        style="margin-top: 30px; text-align: center; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('install.step3-seed') }}"
            style="text-decoration: none; color: #007bff; font-size: 15px; display: flex; align-items: center; transition: color 0.3s ease;">
            &larr; <span style="margin-left: 5px;">Previous Step (Seeding)</span>
        </a>
        <a href="{{ route('install.step5-default-settings') }}"
            style="text-decoration: none; color: #007bff; font-size: 15px; display: flex; align-items: center; transition: color 0.3s ease;">
            Skip Step (Default Settings) <span style="margin-left: 5px;">&rarr;</span>
        </a>
    </div>
</div>
