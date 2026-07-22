<?php

use App\Domains\Availability\Controllers\Web\PartnerAvailabilityController;
use App\Domains\Catalogue\Controllers\Web\PartnerResidenceController;
use App\Domains\Catalogue\Controllers\Web\PartnerVehicleController;
use App\Domains\Catalogue\Controllers\Web\ResidenceController;
use App\Domains\Catalogue\Controllers\Web\SearchController;
use App\Domains\Catalogue\Controllers\Web\VehicleController;
use App\Domains\Identity\Controllers\Web\AuthController;
use App\Domains\Identity\Controllers\Web\ProfileController;
use App\Domains\Partners\Controllers\Web\DashboardController;
use App\Domains\Partners\Controllers\Web\PartnerReservationController;
use App\Domains\Reservation\Controllers\Web\ReservationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes web LOWLY — vues Blade, voir AGENT.md (monolithe modulaire, pas de
| frontend séparé) et docs/engineering/06-blade-tailwind-guidelines.md.
|--------------------------------------------------------------------------
|
| Ces routes ne font que du GET (affichage de page) et réutilisent les
| mêmes Actions que les contrôleurs Api pour le rendu initial. Toute
| mutation (connexion, création, upload...) passe par les composants
| Alpine.js qui appellent /api/v1/* — voir resources/js/alpine/.
|
*/

Route::get('/', [SearchController::class, 'index'])->name('home');
Route::get('residences/{residence}', [ResidenceController::class, 'show'])->name('residences.show');
Route::get('vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');

Route::get('login', [AuthController::class, 'showLogin'])->middleware('guest')->name('login.show');
Route::get('register', [AuthController::class, 'showRegister'])->middleware('guest')->name('register.show');

Route::get('me', [ProfileController::class, 'show'])->middleware('auth')->name('me.show');

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
});

Route::middleware(['auth', 'role:partner'])->prefix('partner')->name('partner.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('residences', [PartnerResidenceController::class, 'index'])->name('residences.index');
    Route::get('residences/create', [PartnerResidenceController::class, 'create'])->name('residences.create');
    Route::get('residences/{residence}/edit', [PartnerResidenceController::class, 'edit'])->name('residences.edit');

    Route::get('vehicles', [PartnerVehicleController::class, 'index'])->name('vehicles.index');
    Route::get('vehicles/create', [PartnerVehicleController::class, 'create'])->name('vehicles.create');
    Route::get('vehicles/{vehicle}/edit', [PartnerVehicleController::class, 'edit'])->name('vehicles.edit');

    Route::get('availability', [PartnerAvailabilityController::class, 'index'])->name('availability.index');

    Route::get('reservations', [PartnerReservationController::class, 'index'])->name('reservations.index');
    Route::get('reservations/{reservation}', [PartnerReservationController::class, 'show'])->name('reservations.show');
});
