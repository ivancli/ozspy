<?php

namespace OzSpy\Http\Resources\Base;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Retailers extends ResourceCollection
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
            'data' => Retailer::collection($this->collection),
        ];
    }
}
