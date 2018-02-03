<?php

namespace OzSpy\Http\Resources\Base;

class Retailer extends ResourceContract
{
    protected const HIDDEN_ATTRIBUTES = ['id', 'country_id', 'active', 'priority'];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $this->hideAttributes();

        $data = parent::toArray($request);

        array_set($data, 'links', [
            'self' => route('api.v1.retailer.show', $this->getKey()),
        ]);

        return $data;
    }
}
