<?php

namespace OzSpy\Models\Base;

use OzSpy\Models\Model;

class WebHistoricalPrice extends Model
{
    protected $fillable = ['amount'];

    protected $hidden = ['id', 'web_product_id', 'updated_at', 'webProduct'];

    /**
     * relationship with WebProduct
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function webProduct()
    {
        return $this->belongsTo(WebProduct::class, 'web_product_id', 'id');
    }
}
