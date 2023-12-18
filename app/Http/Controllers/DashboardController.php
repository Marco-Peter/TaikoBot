<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $user
            ->load([
                'courses',
                'lessons',
                'team:id' => [
                    'courses:id,name' => [
                        'lessons' => function (Builder $query) {
                            $query->select('course_id', 'start')
                                ->orderBy('start', 'asc')->limit(1);
                        },
                    ],
                ]
            ]);

        return Inertia::render('Dashboard', [
            'user' => $user->only(
                'first_name',
                'courses',
                'lessons',
                'team',
            ),
        ]);
    }
}
