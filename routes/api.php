<?php

use App\Domains\Administration\Controllers\Api\AdminListingController;
use App\Domains\Administration\Controllers\Api\AdminPartnerController;
use App\Domains\Administration\Controllers\Api\AdminSettingController;
use App\Domains\Administration\Controllers\Api\AdminStatisticController;
use App\Domains\Administration\Controllers\Api\AdminUserController;
use App\Domains\Availability\Controllers\Api\PartnerAvailabilityBlockController;
use App\Domains\Catalogue\Controllers\Api\PartnerResidenceController;
use App\Domains\Catalogue\Controllers\Api\PartnerResidencePhotoController;
use App\Domains\Catalogue\Controllers\Api\PartnerVehicleController;
use App\Domains\Catalogue\Controllers\Api\ResidenceController;
use App\Domains\Catalogue\Controllers\Api\SearchController;
use App\Domains\Catalogue\Controllers\Api\VehicleController;
use App\Domains\Communication\Controllers\Api\NotificationController;
use App\Domains\Identity\Controllers\Api\AuthController;
use App\Domains\Identity\Controllers\Api\ProfileController;
use App\Domains\Partners\Controllers\Api\PartnerDashboardController;
use App\Domains\Reservation\Controllers\Api\CounterOfferController;
use App\Domains\Reservation\Controllers\Api\PartnerReservationController;
use App\Domains\Reservation\Controllers\Api\ReservationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API interne LOWLY — voir API_GUIDE.md
|--------------------------------------------------------------------------
|
| Ces routes sont l'API interne consommée par les vues Blade et les
| composants Alpine.js (recherche dynamique, calendrier de disponibilité,
| tableau de bord partenaire...) — voir API_GUIDE.md §1. L'authentification
| repose sur la session Laravel standard, pas sur un jeton (API_GUIDE.md §4) :
| le groupe `web` ci-dessous fournit donc la session et la protection CSRF,
| en complément du groupe `api` déjà appliqué globalement par
| bootstrap/app.php (throttling, résolution des bindings de route).
|
| Les contrôleurs enregistrés ici sont des SQUELETTES DE CONCEPTION : ils
| retournent tous une réponse 501 pour le moment. La logique métier
| (Actions/Services par domaine, Policies d'autorisation fine) est
| implémentée en phase Développement — voir ENGINEERING.md §2 et §5.
| L'autorisation par rôle (`role:partner`, `role:admin`) est en revanche
| déjà active au niveau du routage.
|
*/

Route::middleware('web')->prefix('v1')->name('api.v1.')->group(function () {

    // --- Public — voir API_GUIDE.md §9 --------------------------------------
    Route::get('residences', [ResidenceController::class, 'index'])->name('residences.index');
    Route::get('residences/{residence}', [ResidenceController::class, 'show'])->name('residences.show');
    Route::get('vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('search', [SearchController::class, 'index'])->name('search');
    Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');

    // --- Client — voir API_GUIDE.md §10 -------------------------------------
    Route::middleware(['auth', 'role:client'])->group(function () {
        Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
        Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::post('reservations/{reservation}/counter-offers/{counterOffer}/accept', [CounterOfferController::class, 'accept'])
            ->name('reservations.counter-offers.accept');
        Route::post('reservations/{reservation}/counter-offers/{counterOffer}/reject', [CounterOfferController::class, 'reject'])
            ->name('reservations.counter-offers.reject');
        Route::get('me', [ProfileController::class, 'show'])->name('me.show');
        Route::patch('me', [ProfileController::class, 'update'])->name('me.update');
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    });

    // --- Partenaire — voir API_GUIDE.md §11 ---------------------------------
    Route::middleware(['auth', 'role:partner'])->prefix('partner')->name('partner.')->group(function () {
        Route::get('dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');

        Route::get('residences', [PartnerResidenceController::class, 'index'])->name('residences.index');
        Route::post('residences', [PartnerResidenceController::class, 'store'])->name('residences.store');
        Route::patch('residences/{residence}', [PartnerResidenceController::class, 'update'])->name('residences.update');
        Route::post('residences/{residence}/photos', [PartnerResidencePhotoController::class, 'store'])->name('residences.photos.store');
        Route::delete('residences/{residence}/photos/{photo}', [PartnerResidencePhotoController::class, 'destroy'])->name('residences.photos.destroy');

        Route::get('vehicles', [PartnerVehicleController::class, 'index'])->name('vehicles.index');
        Route::post('vehicles', [PartnerVehicleController::class, 'store'])->name('vehicles.store');
        Route::patch('vehicles/{vehicle}', [PartnerVehicleController::class, 'update'])->name('vehicles.update');

        Route::post('availability-blocks', [PartnerAvailabilityBlockController::class, 'store'])->name('availability-blocks.store');
        Route::delete('availability-blocks/{availabilityBlock}', [PartnerAvailabilityBlockController::class, 'destroy'])->name('availability-blocks.destroy');

        Route::get('reservations', [PartnerReservationController::class, 'index'])->name('reservations.index');
        Route::post('reservations/{reservation}/accept', [PartnerReservationController::class, 'accept'])->name('reservations.accept');
        Route::post('reservations/{reservation}/reject', [PartnerReservationController::class, 'reject'])->name('reservations.reject');
        Route::post('reservations/{reservation}/counter-offer', [PartnerReservationController::class, 'counterOffer'])->name('reservations.counter-offer');
    });

    // --- Administration — voir API_GUIDE.md §12 -----------------------------
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('partners/pending', [AdminPartnerController::class, 'pending'])->name('partners.pending');
        Route::post('partners/{partner}/validate', [AdminPartnerController::class, 'validatePartner'])->name('partners.validate');
        Route::post('partners/{partner}/reject', [AdminPartnerController::class, 'reject'])->name('partners.reject');

        Route::get('listings/pending', [AdminListingController::class, 'pending'])->name('listings.pending');
        Route::post('listings/{type}/{id}/validate', [AdminListingController::class, 'validateListing'])->name('listings.validate');
        Route::post('listings/{type}/{id}/reject', [AdminListingController::class, 'reject'])->name('listings.reject');

        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');

        Route::get('statistics', [AdminStatisticController::class, 'index'])->name('statistics.index');

        Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::patch('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});
