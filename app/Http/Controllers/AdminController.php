<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function showAdminPage(): Response
    {
        Gate::authorize('edit-users');

        return Inertia::render('Admin/Show');
    }
}
