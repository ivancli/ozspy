<?php

namespace OzSpy\Http\Resources\Base;

use Illuminate\Http\Resources\Json\Resource;

class WebCategory extends Resource
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

        array_set($data, 'links', [
            'self' => route('api.v1.web-category.show', $this->getKey()),
        ]);

        return $data;
    }
}
