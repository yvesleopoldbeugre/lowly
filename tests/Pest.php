<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

beforeEach(function () {
    // Les routes api/v1 sont volontairement protégées par le middleware
    // `web` (session + CSRF, voir routes/api.php et API_GUIDE.md §4). La
    // vérification CSRF elle-même est un comportement du framework, pas
    // une règle métier LOWLY : elle est désactivée ici pour que les tests
    // Feature portent sur la logique applicative, sans avoir à simuler
    // l'obtention d'un jeton CSRF à chaque requête.
    $this->withoutMiddleware(VerifyCsrfToken::class);

    // Le store de cache `array` (CACHE_STORE=array en environnement de
    // test) persiste entre les tests au sein d'un même process — sans ce
    // flush, le rate limiter `auth` (voir AppServiceProvider) fuit d'un
    // test à l'autre et fait échouer des tests indépendants.
    Cache::flush();
})->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
