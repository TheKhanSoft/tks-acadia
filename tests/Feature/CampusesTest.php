<?php

use App\Models\Campus;
use App\Models\User;
use Livewire\Volt\Volt;
use Livewire\Livewire;

test('campuses component renders correctly', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create some test campuses
    Campus::factory()->count(3)->create();
    
    // Render the component
    $response = $this->get('/campuses');
    $response->assertStatus(200);
    
    // Alternative way to test if you're using Livewire directly
    Livewire::test('campuses')
        ->assertViewIs('livewire.campuses');
});

test('campuses can be searched', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create test campuses with specific name
    $searchableCampus = Campus::factory()->create(['name' => 'Unique Test Campus']);
    Campus::factory()->count(5)->create(); // Create some other campuses
    
    // Test search functionality
    Livewire::test('campuses')
        ->set('search', 'Unique Test')
        ->assertSee('Unique Test Campus')
        ->assertDontSeeCount(Campus::count()); // Should not see all campuses
});

test('campuses can be sorted', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create test campuses with specified names to control sort order
    Campus::factory()->create(['name' => 'Campus C']);
    Campus::factory()->create(['name' => 'Campus A']);
    Campus::factory()->create(['name' => 'Campus B']);
    
    // Test sorting functionality
    Livewire::test('campuses')
        ->call('sortBy', 'name')
        ->assertSet('sortField', 'name')
        ->assertSet('sortDirection', 'asc');
});

test('campus pagination works', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create more campuses than the default per page (10)
    Campus::factory()->count(15)->create();
    
    // Test pagination
    Livewire::test('campuses')
        ->set('perPage', 10)
        ->assertCount('campuses', 10) // Only 10 should be visible
        ->assertViewHas('campuses', function ($campuses) {
            return $campuses->count() === 10;
        });
});

test('can toggle show deleted records', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create a regular campus and a soft-deleted campus
    $activeCampus = Campus::factory()->create(['name' => 'Active Campus']);
    $deletedCampus = Campus::factory()->create(['name' => 'Deleted Campus']);
    $deletedCampus->delete();
    
    // By default shouldn't see deleted campuses
    Livewire::test('campuses')
        ->assertSee('Active Campus')
        ->assertDontSee('Deleted Campus')
        
        // Toggle to show deleted and should see both
        ->set('showDeletedRecords', true)
        ->assertSee('Active Campus')
        ->assertSee('Deleted Campus');
});

test('can create a new campus', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $campusData = [
        'name' => 'New Test Campus',
        'code' => 'NTC',
        'location' => 'Test Location',
        'description' => 'Test Description',
        'address' => 'Test Address',
        'phone' => '123-456-7890',
        'email' => 'campus@test.com',
        'website' => 'https://test.edu',
        'founded_year' => 2000,
        'is_active' => true,
    ];
    
    Livewire::test('campuses')
        ->call('openModal')
        ->assertSet('showModal', true)
        
        // Fill in form data
        ->set('name', $campusData['name'])
        ->set('code', $campusData['code'])
        ->set('location', $campusData['location'])
        ->set('description', $campusData['description'])
        ->set('address', $campusData['address'])
        ->set('phone', $campusData['phone'])
        ->set('email', $campusData['email'])
        ->set('website', $campusData['website'])
        ->set('founded_year', $campusData['founded_year'])
        ->set('is_active', $campusData['is_active'])
        
        // Save the form
        ->call('save')
        
        // Modal should be closed and campus should be in database
        ->assertSet('showModal', false);
    
    // Check that campus was created in the database
    $this->assertDatabaseHas('campuses', [
        'name' => $campusData['name'],
        'code' => strtoupper($campusData['code']), // Code is converted to uppercase
    ]);
});

test('can update an existing campus', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create a campus to edit
    $campus = Campus::factory()->create([
        'name' => 'Original Campus Name',
        'code' => 'OCN',
    ]);
    
    Livewire::test('campuses')
        ->call('openModal', $campus->id)
        ->assertSet('showModal', true)
        ->assertSet('campusId', $campus->id)
        ->assertSet('name', $campus->name)
        ->assertSet('code', $campus->code)
        
        // Change campus name
        ->set('name', 'Updated Campus Name')
        
        // Save the form
        ->call('save')
        
        // Modal should be closed
        ->assertSet('showModal', false);
    
    // Check that campus was updated in the database
    $this->assertDatabaseHas('campuses', [
        'id' => $campus->id,
        'name' => 'Updated Campus Name',
    ]);
});

test('can toggle campus active status', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create an active campus
    $campus = Campus::factory()->active()->create();
    
    Livewire::test('campuses')
        // Toggle active status
        ->call('toggleActive', $campus->id);
    
    // Refresh the campus from database
    $campus->refresh();
    
    // Campus should be inactive now
    expect($campus->is_active)->toBeFalse();
    
    Livewire::test('campuses')
        // Toggle again
        ->call('toggleActive', $campus->id);
    
    // Refresh the campus from database
    $campus->refresh();
    
    // Campus should be active again
    expect($campus->is_active)->toBeTrue();
});

test('can soft delete a campus', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create a campus to delete
    $campus = Campus::factory()->create();
    
    Livewire::test('campuses')
        // Confirm deletion
        ->call('confirmDelete', $campus->id)
        ->assertSet('confirmingDeletion', true)
        ->assertSet('campusId', $campus->id)
        
        // Delete the campus
        ->call('delete')
        ->assertSet('confirmingDeletion', false);
    
    // Campus should be soft deleted
    expect(Campus::find($campus->id))->toBeNull();
    expect(Campus::withTrashed()->find($campus->id))->not->toBeNull();
});

test('can restore a deleted campus', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create a campus and delete it
    $campus = Campus::factory()->create();
    $campus->delete();
    
    Livewire::test('campuses')
        // Show deleted records
        ->set('showDeletedRecords', true)
        
        // Confirm restoration
        ->call('confirmRestore', $campus->id)
        ->assertSet('confirmingRestore', true)
        ->assertSet('campusId', $campus->id)
        
        // Restore the campus
        ->call('restore')
        ->assertSet('confirmingRestore', false);
    
    // Campus should be restored
    expect(Campus::find($campus->id))->not->toBeNull();
});

test('can permanently delete a campus', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create a campus and delete it
    $campus = Campus::factory()->create();
    $campus->delete();
    
    Livewire::test('campuses')
        // Show deleted records
        ->set('showDeletedRecords', true)
        
        // Confirm permanent deletion
        ->call('confirmPermanentDelete', $campus->id)
        ->assertSet('confirmingPermanentDeletion', true)
        ->assertSet('campusId', $campus->id)
        
        // Permanently delete the campus
        ->call('permanentDelete')
        ->assertSet('confirmingPermanentDeletion', false);
    
    // Campus should be permanently deleted
    expect(Campus::withTrashed()->find($campus->id))->toBeNull();
});

test('can bulk delete campuses', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create campuses to delete
    $campuses = Campus::factory()->count(3)->create();
    $campusIds = $campuses->pluck('id')->map(fn($id) => (string) $id)->toArray();
    
    Livewire::test('campuses')
        // Select campuses
        ->set('selectedCampuses', $campusIds)
        
        // Confirm bulk deletion
        ->call('confirmBulkDelete')
        ->assertSet('confirmingBulkDeletion', true)
        
        // Delete the campuses
        ->call('bulkDelete')
        ->assertSet('confirmingBulkDeletion', false)
        ->assertSet('selectedCampuses', []);
    
    // All selected campuses should be soft deleted
    foreach ($campusIds as $id) {
        expect(Campus::find($id))->toBeNull();
        expect(Campus::withTrashed()->find($id))->not->toBeNull();
    }
});

test('validation prevents creating invalid campus', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    Livewire::test('campuses')
        ->call('openModal')
        ->assertSet('showModal', true)
        
        // Try to save without required fields
        ->set('name', '')
        ->set('code', '')
        ->call('save')
        
        // Modal should still be open due to validation errors
        ->assertSet('showModal', true)
        
        // Should have validation errors
        ->assertHasErrors(['name', 'code']);
});

test('validation prevents duplicate campus code', function () {
    // Create a user and authenticate
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Create a campus with a specific code
    $existingCampus = Campus::factory()->create(['code' => 'UNIQUE']);
    
    Livewire::test('campuses')
        ->call('openModal')
        ->assertSet('showModal', true)
        
        // Try to create a new campus with the same code
        ->set('name', 'Another Campus')
        ->set('code', 'UNIQUE')
        ->call('save')
        
        // Modal should still be open due to validation errors
        ->assertSet('showModal', true)
        
        // Should have validation error on code
        ->assertHasErrors(['code']);
});