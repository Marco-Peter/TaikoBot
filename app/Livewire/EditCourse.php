<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\IncomeGroup;
use App\Models\Lesson;
use App\Models\Team;
use Livewire\Attributes\Rule;
use Livewire\Component;

class EditCourse extends Component
{
    public Course $course;

    #[Rule('required|string|min:3')]
    public string $name = '';

    #[Rule('required|min:3')]
    public string $description = '';

    #[Rule('required|int')]
    public int $capacity = 0;

    public array $fees = [];

    #[Rule('required|string|min:3')]
    public string $lesson_title = '';

    #[Rule('required|date')]
    public  $lesson_start;

    #[Rule('required|date')]
    public  $lesson_end;

    public $teams = [];
    public $selectedTeams = [];

    public $invitees = [];
    public $participants = [];
    public $paid = [];

    public function mount(Course $course)
    {
        $this->course = $course;

        $this->name = $course->name;
        $this->description = $course->description;
        $this->capacity = $course->capacity;

        $this->teams = Team::all()->toArray();

        foreach ($course->incomeGroups as $incomeGroup) {
            $this->fees[$incomeGroup->id] = ["name" => $incomeGroup->name, "fee" => strval($incomeGroup->pivot->fee)];
        }
        $this->selectedTeams = $course->teams->pluck('id')->toArray();
        $this->invitees = $course->invitees()->with(['team', 'incomeGroup'])->get()->toArray();
        $this->participants = $course->participants->pluck('id')->all();
        $this->paid = $course->participants_paid()->get()->pluck('id')->all();
    }

    public function update_selectedTeams()
    {
        $this->course->teams()->sync($this->selectedTeams);
        $this->invitees = $this->course->invitees()->with(['team', 'incomeGroup'])->get()->toArray();
    }

    public function update_participants()
    {
        $this->course->participants()->sync($this->participants);
        $this->paid = $this->course->participants_paid()->get()->pluck('id')->all();
    }

    public function update_payment(int $id)
    {
        $this->course->participants()->updateExistingPivot($id, ['paid' => in_array($id, $this->paid)]);
    }

    public function remove_lesson(int $id)
    {
        Lesson::find($id)->delete();
    }

    public function add_lesson()
    {
        $this->validate();
        $this->course->lessons()->create([
            'start' => $this->lesson_start,
            'finish' => $this->lesson_end,
            'title' => $this->lesson_title,
            'notes' => '',
        ]);
        $this->reset('lesson_start', 'lesson_end', 'lesson_title');
    }

    public function render()
    {
        return view('livewire.edit-course');
    }

    public function save()
    {
        $this->validate();
        $this->course->save();
        dd($this->incomeGroup);
        $this->course->fees();
    }
}
