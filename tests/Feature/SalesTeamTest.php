<?php

use App\Models\User;
use App\Models\SalesTeam;
use Spatie\Permission\Models\Role;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Superadmin');
});

test('admin can access sales teams page', function () {
    $response = $this->actingAs($this->admin)->get(route('sales-teams.index'));
    
    // Page renders successfully without routing exceptions
    $response->assertStatus(200);
});

test('admin can create and update a sales team', function () {
    // 1. Create team
    $responseCreate = $this->actingAs($this->admin)->post(route('sales-teams.store'), [
        'name' => 'Tim Sales Alpha',
        'leader_id' => $this->admin->id,
        'monthly_target' => 50000000,
        'notes' => 'Catatan Alpha',
    ]);

    $responseCreate->assertRedirect(route('sales-teams.index'));
    $this->assertDatabaseHas('sales_teams', [
        'name' => 'Tim Sales Alpha',
        'leader_id' => $this->admin->id,
        'monthly_target' => 50000000,
    ]);

    $team = SalesTeam::where('name', 'Tim Sales Alpha')->first();

    // 2. Update team
    $responseUpdate = $this->actingAs($this->admin)->put(route('sales-teams.update', ['sales_team' => $team->id]), [
        'name' => 'Tim Sales Alpha Updated',
        'leader_id' => $this->admin->id,
        'monthly_target' => 60000000,
        'notes' => 'Catatan Alpha Baru',
    ]);

    $responseUpdate->assertRedirect(route('sales-teams.index'));
    $this->assertDatabaseHas('sales_teams', [
        'id' => $team->id,
        'name' => 'Tim Sales Alpha Updated',
        'monthly_target' => 60000000,
    ]);
});
