<?php

use App\Domains\Catalogue\Controllers\Web\ResidenceController;
use App\Domains\Catalogue\Controllers\Web\SearchController;
use App\Domains\Catalogue\Controllers\Web\VehicleController;
use App\Domains\Identity\Controllers\Web\AuthController;
use App\Domains\Identity\Controllers\Web\ProfileController;
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
