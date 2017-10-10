<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use IvanCLI\UM\UMRole;

class Role extends UMRole
{
    public static function getAdminRole()
    {
        return self::where('name', 'admin')->first();
    }

    public static function getClientRole()
    {
        return self::where('name', 'client')->first();
    }
}
