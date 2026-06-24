<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'description'        => $this->description,
            'capacity'           => $this->capacity,
            'signout_limit'      => $this->signout_limit,
            'teacher_payment'    => $this->teacher_payment,
            'assist_payment'     => $this->assist_payment,
            'participants_count' => $this->whenCounted('participants'),
            'teams'              => $this->whenLoaded('teams', fn() => $this->teams->map(fn($t) => [
                'id'   => $t->id,
                'name' => $t->name,
            ])),
            'lessons'            => LessonResource::collection($this->whenLoaded('lessons')),
            'material'           => CourseMaterialResource::collection($this->whenLoaded('material')),
            'compensations'      => $this->whenLoaded('compensations', fn() => $this->compensations->map(fn($c) => [
                'id'   => $c->id,
                'name' => $c->name,
            ])),
            'participants'       => UserResource::collection($this->whenLoaded('participants')),
            'first_lesson_start' => $this->whenLoaded('firstLesson', fn() => $this->firstLesson?->start->toIso8601String()),
            'last_lesson_finish' => $this->whenLoaded('lastLesson', fn() => $this->lastLesson?->finish->toIso8601String()),
        ];
    }
}
