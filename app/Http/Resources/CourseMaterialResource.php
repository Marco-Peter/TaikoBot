<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseMaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'notes'    => $this->notes,
            'external' => (bool) $this->external,
            // path is the download URL for external materials; for internal files use the download endpoint
            'path'     => $this->when($this->external, $this->path),
        ];
    }
}
