<?php

namespace OzSpy\Http\Resources\Base;

use Illuminate\Http\Resources\Json\Resource;

class WebProduct extends Resource
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

        array_set($data, 'prices', [
            'amount' => !is_null($this->recentWebHistoricalPrice) ? $this->recentWebHistoricalPrice->amount : null,
            'recent' => new WebHistoricalPrice($this->recentWebHistoricalPrice),
            'previous' => new WebHistoricalPrice($this->previousWebHistoricalPrice),
        ]);

        array_set($data, 'relation', [
            'categories' => new WebCategories($this->webCategories),
            'retailer' => new Retailer($this->retailer),
        ]);

        array_set($data, 'links', [
            'self' => route('api.v1.web-product.show', $this->getKey()),
        ]);

        return $data;
    }
}
