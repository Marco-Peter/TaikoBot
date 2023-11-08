<?php

namespace App\Livewire;

use App\Enums\LessonParticipationEnum;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Livewire\Component;

class MyLessons extends Component
{
    public User $user;

    public function mount(Request $request)
    {
        $this->user = $request->user();
    }

    public function signOut(Lesson $lesson)
    {
        $lesson->participants()->updateExistingPivot($this->user, [
            'participation' => LessonParticipationEnum::SIGNED_OUT->value,
        ]);
    }

    public function render()
    {
        return view('livewire.my-lessons');
    }
}
