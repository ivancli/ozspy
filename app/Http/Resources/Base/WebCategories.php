<?php

namespace OzSpy\Http\Resources\Base;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WebCategories extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => WebCategory::collection($this->collection),
        ];
    }
}
