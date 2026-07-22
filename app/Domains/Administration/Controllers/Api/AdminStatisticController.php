<?php

namespace App\Domains\Administration\Controllers\Api;

use App\Domains\Administration\Actions\GetPlatformStatistics;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Domaine Administration — voir API_GUIDE.md §12, PRODUCT.md §11
 * (indicateurs de succès), UX_UI.md §7.4.
 */
class AdminStatisticController extends Controller
{
    public function index(Request $request, GetPlatformStatistics $action): JsonResponse
    {
        return response()->json([
            'data' => [
                'type' => 'statistics',
                'attributes' => $action->executer(),
            ],
        ]);
    }
}
