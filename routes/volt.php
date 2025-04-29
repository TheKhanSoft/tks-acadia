<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Define Volt routes here

// Route for the Office Types component
Volt::route('office-types', 'office-types') 
    //->middleware(['auth']) 
    ->name('office-types.index'); 

Volt::route('offices', 'offices') 
    //->middleware(['auth']) 
    ->name('offices.index'); 

// Add other Volt component routes below

Volt::route('campuses', 'campuses');
