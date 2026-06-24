<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'nickname'          => $this->nickname,
            'profile_photo_url' => $this->profile_photo_url,
            'participation'     => $this->pivot->participation->value,
            'message'           => $this->pivot->message,
        ];
    }
}
