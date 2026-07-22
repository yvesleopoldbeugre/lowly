<?php

use App\Domains\Administration\Models\PlatformSetting;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;

it('redirects a guest to login when visiting an admin page', function () {
    $this->get('/admin/partners')->assertRedirect(route('login.show'));
});

it('rejects a non-admin user from admin pages', function () {
    $client = User::factory()->client()->create();

    $this->actingAs($client)->get('/admin/partners')->assertStatus(403);
});

it('renders the pending partners page', function () {
    $admin = User::factory()->admin()->create();
    $pending = Partner::factory()->create();

    $response = $this->actingAs($admin)->get('/admin/partners');

    $response->assertOk();
    $response->assertViewIs('pages.admin.partners.index');
    $response->assertSee('Document légal manquant');
});

it('renders the pending listings page combining residences and vehicles', function () {
    $admin = User::factory()->admin()->create();
    $residence = Residence::factory()->enValidation()->create(['title' => 'Villa En Attente']);
    $vehicle = Vehicle::factory()->enValidation()->create(['brand' => 'Toyota', 'model' => 'Yaris']);

    $response = $this->actingAs($admin)->get('/admin/listings');

    $response->assertOk();
    $response->assertSee('Villa En Attente');
    $response->assertSee('Toyota Yaris');
});

it('renders the users page with role and status filters applied', function () {
    $admin = User::factory()->admin()->create();
    $client = User::factory()->client()->create(['full_name' => 'Aminata Client']);
    $suspended = User::factory()->partner()->create(['full_name' => 'Partenaire Suspendu', 'suspended_at' => now()]);

    $response = $this->actingAs($admin)->get('/admin/users?role=client');

    $response->assertOk();
    $response->assertSee('Aminata Client');
    $response->assertDontSee('Partenaire Suspendu');
});

it('renders the statistics page', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin/statistics');

    $response->assertOk();
    $response->assertSee("Taux d'acceptation des demandes");
});

it('renders the settings page with the seeded delays', function () {
    $admin = User::factory()->admin()->create();
    PlatformSetting::factory()->create([
        'key' => 'reservation_response_delay_hours',
        'value' => ['hours' => 48],
    ]);

    $response = $this->actingAs($admin)->get('/admin/settings');

    $response->assertOk();
    $response->assertSee('Délai de réponse partenaire');
});
