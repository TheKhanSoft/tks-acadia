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

Volt::route('/install/step1-database', 'install.step1-database')->name('install.step1-database');
Volt::route('/install/step2-migrate', 'install.step2-migrate')->name('install.step2-migrate');
Volt::route('/install/step3-seed', 'install.step3-seed')->name('install.step3-seed');
Volt::route('/install/step4-admin-details', 'install.step4-admin-details')->name('install.step4-admin-details');
Volt::route('/install/step5-default-settings', 'install.step5-default-settings')->name('install.step5-default-settings');

    // Country, State, City Routes
Route::prefix('locations')
    // ->middleware(['auth', 'verified'])
    ->group(function () {
    Volt::route('countries', 'locations.country');
    Volt::route('states', 'locations.state');
    Volt::route('cities', 'locations.city');
});
