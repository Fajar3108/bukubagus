<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $total_rating = collect($this->ratings)->count();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'pages' => $this->pages,
            'authors' => $this->authors,
            'average_rating' => collect($this->ratings)->avg('rating') ?? 0,
            'total_reviews' => collect($this->reviews)->count(),
            'reviews' => $this->reviews,
            'total_rating' => $total_rating,
            'ratings' => [
                'star_1' => $this->ratingPercentage($total_rating, 1),
                'star_2' => $this->ratingPercentage($total_rating, 2),
                'star_3' => $this->ratingPercentage($total_rating, 3),
                'star_4' => $this->ratingPercentage($total_rating, 4),
                'star_5' => $this->ratingPercentage($total_rating, 5),
            ],
        ];
    }

    public function ratingPercentage($total_rating, $rating)
    {
        if (!$total_rating) return 0;

        return collect($this->ratings)->where('rating', $rating)->count() / $total_rating * 100;

    }
}
