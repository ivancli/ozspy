<?php

namespace OzSpy\Http\Resources\Base;

class WebHistoricalPrice extends ResourceContract
{
    protected const HIDDEN_ATTRIBUTES = ['id', 'web_product_id', 'updated_at', 'webProduct'];

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

        return $data;
    }
}
