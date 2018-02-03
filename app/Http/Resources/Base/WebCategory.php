<?php

namespace OzSpy\Http\Resources\Base;

class WebCategory extends ResourceContract
{
    protected const HIDDEN_ATTRIBUTES = ['id', 'retailer_id', 'web_category_id', 'last_crawled_products_count', 'active', 'last_crawled_at', 'pivot'];

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
            'self' => route('api.v1.web-category.show', $this->getKey()),
        ]);

        return $data;
    }
}
