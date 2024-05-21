<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Team;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Course::class);

        $courses = Course::withCount('participants')
            ->with([
                'teams:id',
                'lessons' => function (Builder $query) {
                    $query->orderBy('start', 'asc')
                        ->select('id', 'course_id', 'title', 'start', 'finish');
                },
                'lessons.teachers:id,first_name,last_name',
                'participants:id,first_name,last_name',
                'material:id,course_id,path,name,external,notes',
            ])->get();

        $teams = Team::with([
            'users:id,first_name,last_name,team_id',
        ])->get(['id', 'name']);

        return [
            'courses' => $courses,
            'teams' => $teams,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }
}
