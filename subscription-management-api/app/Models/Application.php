<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Application extends Model
{
    protected $table = 'applications';

    protected $fillable = [
        'app_id',
        'provider_credentials',
        'callback_url',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'app_id' => 'string',
            'provider_credentials' => 'json',
            'callback_url' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
