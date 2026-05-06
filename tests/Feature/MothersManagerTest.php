<?php

use App\Imports\MothersImport;
use App\Livewire\Dashboard\MothersManager;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;

it('shows organization selector for system admin', function () {
    $role = Role::create(['name' => 'system-admin']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    Livewire::test(MothersManager::class)
        ->assertSee('Select Organization');
});

it('does not show organization selector for non-system admin', function () {
    $role = Role::create(['name' => 'admin']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    Livewire::test(MothersManager::class)
        ->assertDontSee('Select Organization');
});

it('requires organization_id for system admin during preview', function () {
    $role = Role::create(['name' => 'system-admin']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    Livewire::test(MothersManager::class)
        ->set('file', UploadedFile::fake()->create('mothers.xlsx'))
        ->call('preview')
        ->assertHasErrors(['organization_id' => 'required']);
});

it('does not require organization_id for non-system admin during preview', function () {
    $role = Role::create(['name' => 'admin']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    Excel::fake();

    Livewire::test(MothersManager::class)
        ->set('file', UploadedFile::fake()->create('mothers.xlsx'))
        ->call('preview')
        ->assertHasNoErrors(['organization_id']);
});

it('passes organization_id to MothersImport', function () {
    $role = Role::create(['name' => 'system-admin']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->actingAs($user);

    $org = Organization::factory()->create();
    $file = UploadedFile::fake()->create('mothers.xlsx');

    Excel::fake();

    Livewire::test(MothersManager::class)
        ->set('organization_id', $org->id)
        ->set('file', $file)
        ->call('confirmImport');

    Excel::assertImported('mothers.xlsx', function (MothersImport $import) use ($org) {
        return (int) $import->organizationId === (int) $org->id;
    });
});
