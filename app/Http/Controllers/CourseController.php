<?php

namespace App\Http\Controllers;

use App\Enums\LessonParticipationEnum;
use App\Models\Course;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected function teams_not_selected(Course $course)
    {
        return Team::whereNotIn('id', function ($query) use ($course) {
            $query->select('team_id')->from('course_team')->where('course_id', '=', $course->id);
        })->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('management.list-courses', [
            'courses' => Course::get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('management.create-course');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //$this->authorize('update', $course);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'string',
            'fee' => 'integer|min:0',
            'capacity' => 'integer|min:1',
        ]);
        $course = Course::create($validated);

        return redirect(route('courses.edit', $course));
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
    public function edit(Course $course): View
    {
        //$this->authorize('update', $course);

        return view('management.edit-course', [
            'course' => $course,
            'teams' => Team::all(),
            'teams_not_selected' => $this->teams_not_selected($course),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        //$this->authorize('update', $course);

        if ($request["changed_item"]) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'string',
                'fee' => 'integer|min:0',
                'capacity' => 'integer|min:1',
            ]);
            $course->update($validated);
        } elseif ($request["remove_team"]) {
            $team_to_remove = Team::find($request["remove_team"]);
            $users_to_remove = $team_to_remove->users;
            $lessons = $course->lessons;

            foreach($lessons as $lesson) {
                foreach($users_to_remove as $user_to_remove) {
                    $lesson->participants()->detach($user_to_remove->id);
                }
            }
            $course->teams()->detach($request["remove_team"]);
        } elseif ($request["autosign_off"]) {
            $team = $course->teams()->where('team_id', '=', $request["autosign_off"])->first();
            $lessons = $course->lessons;
            foreach($lessons as $lesson) {
                foreach($lesson->participants as $participant) {
                    $participant->pivot->participation = LessonParticipationEnum::SIGNED_OUT->value;
                    $participant->pivot->save();
                }
            }
            $team->pivot->signed_in = 0;
            $team->pivot->save();
        } elseif ($request["autosign_on"]) {
            $team = $course->teams()->where('team_id', '=', $request["autosign_on"])->first();
            $lessons = $course->lessons;
            foreach($lessons as $lesson) {
                foreach($lesson->participants as $participant) {
                    $participant->pivot->participation = LessonParticipationEnum::SIGNED_IN->value;
                    $participant->pivot->save();
                }
            }
            $team->pivot->signed_in = 1;
            $team->pivot->save();
        } elseif ($request["add_team"]) {
            $new_team = Team::find($request["add_team"]);
            $new_users = $new_team->users;
            $lessons = $course->lessons;
            foreach ($lessons as $lesson) {
                foreach($new_users as $new_user) {
                    $lesson->participants()->attach($new_user->id);
                }
            }
            $course->teams()->attach($request["add_team"]);
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        //$this->authorize('update', $course);

        $course->delete();
        return redirect(route('courses.index'));
    }
}
