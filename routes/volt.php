<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Define Volt routes here

// Route for the Office Types component
Volt::route('office-types', 'office-types') // Assumes the component file is app/Volt/OfficeTypes.php
    //->middleware(['auth']) // Add appropriate middleware (e.g., auth)
    ->name('office-types.index'); // Name the route for easy linking

// Add other Volt component routes below

Volt::route('campuses', 'campuses');
