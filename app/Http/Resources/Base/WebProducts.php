<?php

namespace OzSpy\Http\Resources\Base;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WebProducts extends ResourceCollection
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
            'data' => WebProduct::collection($this->collection),
        ];
    }
}
