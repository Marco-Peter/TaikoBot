<?php

namespace App\Http\Resources;

use App\Enums\LessonParticipationEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $canEditCourses = Gate::check('edit-courses');

        return [
            'id'                => $this->id,
            'course_id'         => $this->course_id,
            'title'             => $this->title,
            'start'             => $this->start->toIso8601String(),
            'finish'            => $this->finish->toIso8601String(),
            // notes only for teachers/admins
            'notes'             => $this->when($canEditCourses, $this->notes),
            // participants included when loaded
            'participants'      => $this->whenLoaded('participants', function () {
                return LessonParticipantResource::collection(
                    $this->participants->filter(
                        fn($p) => $p->pivot->participation !== LessonParticipationEnum::SIGNED_OUT
                    )
                );
            }),
            'teachers'          => $this->whenLoaded('teachers', function () {
                return LessonParticipantResource::collection($this->teachers);
            }),
            'participants_count' => $this->whenCounted('participants'),
            'course'            => $this->whenLoaded('course'),
        ];
    }
}
