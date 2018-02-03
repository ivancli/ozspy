<?php

namespace OzSpy\Http\Resources\Base;

class WebProduct extends ResourceContract
{
    protected const HIDDEN_ATTRIBUTES = ['id', 'retailer_id', 'retailer', 'webCategories', 'webHistoricalPrices', 'recentWebHistoricalPrice', 'previousWebHistoricalPrice'];

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

        array_set($data, 'prices', [
            'amount' => !is_null($this->recentWebHistoricalPrice) ? $this->recentWebHistoricalPrice->amount : null,
            'recent' => new WebHistoricalPrice($this->recentWebHistoricalPrice),
            'previous' => new WebHistoricalPrice($this->previousWebHistoricalPrice),
        ]);

        array_set($data, 'categories', $this->webCategories->pluck('name'));
        array_set($data, 'retailer', $this->retailer->name);

        array_set($data, 'links', [
            'self' => route('api.v1.web-product.show', $this->getKey()),
        ]);

        return $data;
    }
}
