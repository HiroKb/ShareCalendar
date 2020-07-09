<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLogin extends Model
{
    public const UPDATED_AT = null;

    public static function getHistories()
    {
        return self::latest()->get();
    }
}
