<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class InfolombaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $imagePath = implode('/', array_map('rawurlencode', explode('/', $this->image)));

        return [
            'id' => $this->id,
            'image' => $this->image,
            'image_url' => Storage::disk('public')->url($imagePath),
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'start_date' => $this->start_date->toDateString(),
            'end_date' => $this->end_date->toDateString(),
            'status' => $this->status,
            'link' => $this->link,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
