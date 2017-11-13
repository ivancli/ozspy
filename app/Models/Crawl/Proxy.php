<?php

namespace OzSpy\Models\Crawl;

use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    protected $fillable = ['ip', 'port', 'is_active'];
}
