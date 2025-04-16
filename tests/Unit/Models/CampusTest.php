<?php

use App\Models\Campus;
use App\Models\Office;
use App\Http\Requests\CampusRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('campus has the correct fillable attributes', function () {
    $campus = new Campus();
    
    expect($campus->getFillable())->toBe([
        'name', 'location', 'code', 'description', 'address', 
        'phone', 'email', 'website', 'founded_year', 'is_active'
    ]);
});

test('campus casts attributes correctly', function () {
    $campus = new Campus();
    
    expect($campus->getCasts())
        ->toHaveKey('is_active', 'boolean')
        ->toHaveKey('founded_year', 'integer')
        ->toHaveKey('deleted_at', 'datetime');
});

test('campus uses soft deletes', function () {
    // Create a campus
    $campus = Campus::factory()->create([
        'name' => 'Test Campus',
        'code' => 'TEST'
    ]);
    
    // Delete the campus
    $campus->delete();
    
    // Should not be found with normal query
    expect(Campus::find($campus->id))->toBeNull();
    
    // Should be found with withTrashed()
    expect(Campus::withTrashed()->find($campus->id))->not->toBeNull();
    
    // Restore the campus
    $campus->restore();
    
    // Should now be found with normal query
    expect(Campus::find($campus->id))->not->toBeNull();
});

test('campus can have many offices', function () {
    // The relationship method exists and returns the correct type
    $campus = new Campus();
    expect($campus->offices())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
});

test('campus can filter active offices', function () {
    // The relationship method exists and returns the correct type
    $campus = new Campus();
    expect($campus->activeOffices())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
});

test('campus can have many departments', function () {
    // The relationship method exists and returns the correct type
    $campus = new Campus();
    expect($campus->departments())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
});

test('campus can scope to active campuses only', function () {
    // Create one active and one inactive campus
    Campus::factory()->create([
        'name' => 'Active Campus',
        'code' => 'ACTIVE',
        'is_active' => true
    ]);
    
    Campus::factory()->create([
        'name' => 'Inactive Campus',
        'code' => 'INACTIVE',
        'is_active' => false
    ]);
    
    // We should only get the active campus
    expect(Campus::active()->count())->toBe(1);
});

test('code is automatically converted to uppercase via request', function () {
    // Create a request with lowercase code
    $request = new CampusRequest();
    $request->merge(['code' => 'abc123']);
    
    // Use reflection to call the protected prepareForValidation method
    $reflectedRequest = new ReflectionClass($request);
    $prepareForValidation = $reflectedRequest->getMethod('prepareForValidation');
    $prepareForValidation->setAccessible(true);
    $prepareForValidation->invoke($request);
    
    // Check that the code was converted to uppercase
    expect($request->code)->toBe('ABC123');
});