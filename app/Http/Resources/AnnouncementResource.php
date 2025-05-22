<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'description'=>$this->description,
            'operation_type'=>$this->operation_type,
            'price'=>$this->price,
            'is_completed'=>$this->is_completed,
            'is_cancelled'=>$this->is_cancelled,
            'exchange_location_address'=>$this->exchange_location_address,
            'exchange_location_lng'=>$this->exchange_location_lng,
            'exchange_location_lat'=>$this->exchange_location_lat,
            'category' => new CategoryResource($this->category),
        ];
    }
}
