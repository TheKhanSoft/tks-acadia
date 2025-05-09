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

Volt::route('job-natures', 'job-natures') 
    //->middleware(['auth']) 
    ->name('job-natures.index'); 

Volt::route('students', 'students.index')
    //->middleware(['auth']) 
    ->name('students.index');

Volt::route('employee-work-statuses', 'employee-work-statuses') 
    //->middleware(['auth']) 
    ->name('employee-work-statuses.index'); 

// Add other Volt component routes below

Volt::route('campuses', 'campuses');

Volt::route('employees', 'employees')
    //->middleware(['auth']) 
    ->name('employees.index');
    
Volt::route('employee-types', 'employee-types')
    //->middleware(['auth']) 
    ->name('employee-types.index');


    // Country, State, City Routes
Route::prefix('locations')
    // ->middleware(['auth', 'verified'])
    ->group(function () {
    Volt::route('countries', 'locations.country');
    Volt::route('states', 'locations.state');
    Volt::route('cities', 'locations.city');
});