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
            'offset' => 'integer|min:0',
            'per_page' => 'integer|between:1,100',
            'query' => 'max:255|in:' . join(',', self::AVAILABLE_QUERY),
        ];
    }
}
