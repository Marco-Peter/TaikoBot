<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'nickname'          => $this->nickname,
            'email'             => $this->email,
            'role'              => $this->role->value,
            'karma'             => $this->karma,
            'team_id'           => $this->team_id,
            'last_payment'      => $this->last_payment?->toIso8601String(),
            'profile_photo_url' => $this->profile_photo_url,
            'settings'          => $this->settings,
            // open_payment is expensive (2 raw JOINs) — only include for single-user responses
            'open_payment'      => $this->when(
                $request->route('user') !== null || $request->is('api/v1/auth/user'),
                fn() => $this->open_payment
            ),
        ];
    }
}
