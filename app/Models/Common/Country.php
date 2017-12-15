<?php

namespace OzSpy\Models\Common;

use OzSpy\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'official_name', 'cca2', 'cca3', 'ccn3', 'region', 'subregion'];

    protected $dates = ['deleted_at'];
}
