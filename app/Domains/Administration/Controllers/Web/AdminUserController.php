<?php

namespace App\Domains\Administration\Controllers\Web;

use App\Domains\Administration\Actions\ListUsers;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Domaine Administration — gestion des utilisateurs, voir UX_UI.md §7.3.
 */
class AdminUserController extends Controller
{
    public function index(Request $request, ListUsers $action): View
    {
        return view('pages.admin.users.index', [
            'title' => 'Utilisateurs',
            'users' => $action->executer([
                'role' => $request->string('role')->value() ?: null,
                'status' => $request->string('status')->value() ?: null,
            ]),
            'role' => $request->string('role')->value(),
            'status' => $request->string('status')->value(),
        ]);
    }
}
