<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\IncomeGroup;
use Livewire\Attributes\Rule;
use Livewire\Component;

class CreateCourse extends Component
{
    #[Rule('required|string|min:3')]
    public string $name = '';

    #[Rule('required|min:3')]
    public string $description = '';

    #[Rule('required|int')]
    public int $capacity = 0;

    public array $fees = [];

    public function render()
    {
        return view('livewire.create-course')
            ->with(['incomeGroups' => IncomeGroup::all()]);
    }

    public function save()
    {
        $this->validate();

        $course = Course::create($this->only([
            'name',
            'description',
            'capacity',
        ]));

        $course->incomeGroups()->sync($this->fees);
        return $this->redirect(route('course.edit', $course));
    }
}
