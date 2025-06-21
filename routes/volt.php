<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::prefix('install')->name('install')->group(function () {
    // Volt::route('/', 'install.step1-database');
    Route::get('/', function () {
        return redirect('install/step1-database');
    });
    Volt::route('step1-database', 'install.step1-database')->name('.step1-database');
    Volt::route('step2-migrate', 'install.step2-migrate')->name('.step2-migrate');
    Volt::route('step3-seed', 'install.step3-seed')->name('.step3-seed');
    Volt::route('step4-admin-details', 'install.step4-admin-details')->name('.step4-admin-details');
    Volt::route('step5-default-settings', 'install.step5-default-settings')->name('.step5-default-settings');
});

// Route for the Office Types component
// Volt::route('office-types', 'office-types') 
//     //->middleware(['auth']) 
//     ->name('office-types.index'); 

// Volt::route('offices', 'offices') 
//     //->middleware(['auth']) 
//     ->name('offices.index'); 

// Route for the Office Types component
Route::prefix('offices')
    ->name('offices.')
    //->middleware(['auth']) 
    ->group(function () {
    Volt::route('/', 'offices.index')->name('index');
    Volt::route('/types', 'offices.types')->name('types');
});    

Volt::route('job-natures', 'job-natures') 
    //->middleware(['auth']) 
    ->name('job-natures.index'); 

    
#region EMPLOYEE RELATED ROUTES
Route::prefix('employees')
    ->name('employees.')
    // ->middleware(['auth'])
    ->group(function () {
    Volt::route('/', 'employees.index')->name('index');
    Volt::route('/work-statuses', 'employees.work-statuses')->name('work-statuses');
});  
#endregion

// Add other Volt component routes below
Volt::route('campuses', 'campuses')->name('campuses.index');

    //Students related routes
Route::prefix('students')->name('students')->group(function () {
    Volt::route('/', 'students.index')->name('index');
});

Route::prefix('subjects')->name('subjects.')->group(function () {
    Volt::route('/', 'subjects.index')->name('index');
    Volt::route('/types', 'subjects.types')->name('types');
    Volt::route('/department-subjects', 'subjects.department-subjects')->name('department-subjects');
    Volt::route('/slos', 'subjects.learning-outcomes')->name('subject-learning-outcomes');
});

Route::prefix('programs')->name('programs.')->group(function () {
    Volt::route('/', 'programs.index')->name('index');
});

Route::prefix('faculties')->name('faculties.')->group(function () {
    Volt::route('/', 'faculties.index')->name('index');
    
});

// Country, State, City Routes
Route::prefix('locations')
    // ->middleware(['auth', 'verified'])
    ->group(function () {
    Volt::route('countries', 'locations.country');
    Volt::route('states', 'locations.state');
    Volt::route('cities', 'locations.city');
});
