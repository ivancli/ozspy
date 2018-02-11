<?php

namespace OzSpy\Http\Requests\Models\WebProducts;

use Illuminate\Foundation\Http\FormRequest;

class LoadRequest extends FormRequest
{
    protected const AVAILABLE_QUERY = ['recent_price', 'price_change', 'price_drop', 'price_raise'];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => 'integer|min:1',
            'per_page' => 'integer|between:1,100',
            'query' => 'max:255|in:' . join(',', self::AVAILABLE_QUERY),
            'filter.retailer' => 'array|max:10',
            'filter.retailer.*' => 'string|min:1|max:255',
            'filter.category' => 'array|max:10',
            'filter.category.*' => 'string|min:1|max:255',
            'filter.max_recent_price' => 'numeric|min:0',
            'filter.max_previous_price' => 'numeric|min:0',
            'filter.min_recent_price' => 'numeric|min:0',
            'filter.min_previous_price' => 'numeric|min:0',
        ];
    }
}
