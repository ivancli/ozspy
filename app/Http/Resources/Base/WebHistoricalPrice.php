<?php

namespace OzSpy\Http\Resources\Base;

use Illuminate\Http\Resources\Json\Resource;

class WebHistoricalPrice extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);

        return $data;
    }
}
