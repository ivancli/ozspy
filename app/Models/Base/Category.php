<?php

namespace OzSpy\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    protected $dates = ['deleted_at'];

    /**
     * relationship with retailer
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'retailer_id', 'id');
    }

    /**
     * relationship with category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentCategory()
    {
        return $this->belongsTo(self::class, 'category_id', 'id');
    }
}
