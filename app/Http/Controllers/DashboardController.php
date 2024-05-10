<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                    ->where('start', '>', Carbon::now()->toDateString())
                    ->oldest('start');
            },
            'lessons.course:id,name',
            'lessons.teachers:id,name',
        ]);
    }
}
