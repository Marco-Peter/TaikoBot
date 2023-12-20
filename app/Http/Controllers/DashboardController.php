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
        $user = Auth::user()->load([
            'lessons' => function (Builder $query) {
                $query->select('id', 'course_id', 'title', 'start')
                    ->orderBy('start', 'asc');
            },
            'lessons.course:id,name',
        ]);

        $coursesSignedUp = $user->courses->load([
            'lessons' => function (Builder $query) {
                $query->select('course_id', 'start')
                    ->orderBy('start', 'asc');
            },
        ]);

        $coursesNotSignedUp = $user->team->courses->diff($coursesSignedUp)->load([
            'lessons' => function (Builder $query) {
                $query->select('course_id', 'start')
                    ->orderBy('start', 'asc');
            },
        ]);
        //dd($coursesSignedUp);

        return Inertia::render('Dashboard', [
            'user' => $user->only('id', 'first_name', 'lessons'),
            'coursesSignedUp' => $coursesSignedUp,
            'coursesNotSignedUp' => $coursesNotSignedUp,
        ]);
    }
}
