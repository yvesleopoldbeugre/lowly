<?php

namespace App\Domains\Administration\Controllers\Web;

use App\Domains\Administration\Actions\GetPlatformStatistics;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Administration — statistiques globales, voir PRODUCT.md §11,
 * UX_UI.md §7.4.
 */
class AdminStatisticController extends Controller
{
    public function index(Request $request, GetPlatformStatistics $action): View
    {
        return view('pages.admin.statistics.index', [
            'title' => 'Statistiques',
            'statistics' => $action->executer(),
        ]);
    }
}
