<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [AuthController::class, 'showLogin'])->middleware('guest')->name('login.show');
Route::get('register', [AuthController::class, 'showRegister'])->middleware('guest')->name('register.show');

Route::get('me', [ProfileController::class, 'show'])->middleware('auth')->name('me.show');
