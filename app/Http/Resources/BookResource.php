<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'isbn' => $this->isbn,
            'authors' => $this->authors,
            'average_rating' => collect($this->ratings)->avg('rating') ?? 0,
        ];
    }
}
