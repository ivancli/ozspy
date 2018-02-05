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

        $attributes = $request->has('attributes') && is_array($request->get('attributes')) ? $request->get('attributes') : [];

        $this->setPrices($data, $attributes);

        array_set($data, 'categories', $this->webCategories->pluck('name'));
        array_set($data, 'retailer', $this->retailer->name);

        array_set($data, 'links', [
            'self' => route('api.v1.web-product.show', $this->getKey()),
        ]);

        return $data;
    }

    protected function setPrices(&$data, $attributes)
    {
        if (in_array('recent_price', $attributes) || in_array('previous_price', $attributes)) {
            $prices = [];
            if (in_array('recent_price', $attributes)) {
                array_set($prices, 'amount', !is_null($this->recentWebHistoricalPrice) ? $this->recentWebHistoricalPrice->amount : null);
                array_set($prices, 'recent', new WebHistoricalPrice($this->recentWebHistoricalPrice));
            }
            if (in_array('previous_price', $attributes)) {
                array_set($prices, 'previous', new WebHistoricalPrice($this->previousWebHistoricalPrice));
            }
            array_set($data, 'prices', $prices);
        }
    }
}
