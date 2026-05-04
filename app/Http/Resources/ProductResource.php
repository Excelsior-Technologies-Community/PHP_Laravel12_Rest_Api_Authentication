<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'detail' => $this->detail,
            'status' => $this->status,
            'created_by' => [
                'id' => $this->created_by,
                'name' => $this->user?->name,
                'avatar' => $this->user?->avatar ? asset('avatars/' . $this->user->avatar) : null,
            ],
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->format('d/m/Y'),
            'updated_at' => $this->updated_at?->format('d/m/Y'),
        ];
    }
}