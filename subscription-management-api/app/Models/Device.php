<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Scout\Searchable;

final class Device extends Authenticatable
{
    use Searchable;

    protected $table = 'devices';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uid',
        'app_id',
        'language',
        'operating_system',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'uid' => 'string',
            'app_id' => 'string',
            'language' => 'string',
            'operating_system' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }
}
