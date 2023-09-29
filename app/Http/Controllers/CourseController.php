<?php

namespace App\Http\Controllers;

use App\Enums\LessonParticipationEnum;
use App\Models\Course;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
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
            $this->change_item($request, $course);
        } elseif ($request["remove_team"]) {
            $this->remove_team($request, $course);
        } elseif ($request["autosign_off"]) {
            $this->autosign_off($request, $course);
        } elseif ($request["autosign_on"]) {
            $this->autosign_on($request, $course);
        } elseif ($request["add_team"]) {
            $this->add_team($request, $course);
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

    private function change_item(Request $request, Course $course): void
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'string',
            'fee' => 'integer|min:0',
            'capacity' => 'integer|min:1',
        ]);
        $course->update($validated);
    }

    private function add_team(Request $request, Course $course): void
    {
        $new_users = $this->get_changing_users(Team::find($request["add_team"]), $course);
        $lessons = $course->lessons;
        foreach ($lessons as $lesson) {
            $lesson->participants()->attach($new_users->modelKeys());
        }
        $course->teams()->attach($request["add_team"]);
    }

    private function remove_team(Request $request, Course $course): void
    {
        $course->teams()->detach($request["remove_team"]);
        $users_to_remove = $this->get_changing_users(Team::find($request["remove_team"]), $course);
        $lessons = $course->lessons;
        foreach ($lessons as $lesson) {
            $lesson->participants()->detach($users_to_remove->modelKeys());
        }
    }

    private function get_changing_users(Team $changing_team, Course $course): Collection
    {
        $changing_users = $changing_team->users;
        $remaining_teams = $course->load('teams')->teams;
        foreach($remaining_teams as $remaining_team) {
            $changing_users = $changing_users->diff($remaining_team->users);
        }
        return $changing_users;
    }

    private function autosign_on(Request $request, Course $course): void
    {
        $team = $course->teams()->where('team_id', '=', $request["autosign_on"])->first();
        $lessons = $course->lessons;
        foreach ($lessons as $lesson) {
            $participants = $lesson->participants->where('team_id', $team->id);
            foreach ($participants as $participant) {
                $participant->pivot->participation = LessonParticipationEnum::SIGNED_IN->value;
                $participant->pivot->save();
            }
        }
        $team->pivot->signed_in = 1;
        $team->pivot->save();
    }

    private function autosign_off(Request $request, Course $course)
    {
        $team = $course->teams()->where('team_id', '=', $request["autosign_off"])->first();
        $lessons = $course->lessons;
        foreach ($lessons as $lesson) {
            $participants = $lesson->participants->where('team_id', $team->id);
            foreach ($participants as $participant) {
                $participant->pivot->participation = LessonParticipationEnum::SIGNED_OUT->value;
                $participant->pivot->save();
            }
        }
        $team->pivot->signed_in = 0;
        $team->pivot->save();
    }
}
