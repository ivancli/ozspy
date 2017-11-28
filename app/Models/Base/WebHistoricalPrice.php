<?php

namespace OzSpy\Models\Base;

use Illuminate\Database\Eloquent\Model;

class WebHistoricalPrice extends Model
{
    protected $fillable = ['amount'];

    /**
     * relationship with WebProduct
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function webProduct()
    {
        return $this->belongsTo(WebProduct::class, 'web_product_id', 'id');
    }
}
