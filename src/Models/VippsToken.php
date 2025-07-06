<?php

namespace Wirement\Vipps\Models;

use Illuminate\Database\Eloquent\Model;

class VippsToken extends Model
{
    protected $table = 'vipps_tokens';

    protected $fillable = [
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
