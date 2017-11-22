<?php

namespace OzSpy\Models\Crawl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proxy extends Model
{
    use SoftDeletes;

    protected $fillable = ['ip', 'port', 'provider'];

    protected $dates = ['deleted_at'];
}