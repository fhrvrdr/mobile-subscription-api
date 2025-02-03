<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

final class Subscription extends Model
{
    use Searchable;

    protected $table = 'subscriptions';

    protected $fillable = [
        'device_id',
        'receipt',
        'status',
        'expires_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'device_id' => 'int',
            'receipt' => 'string',
            'status' => 'int',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        self::creating(function (Subscription $subscription) {
            $subscription->updated_at = null;
        });
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $array['expires_at'] = $this->expires_at->timestamp;
        $array['created_at'] = $this->created_at->timestamp;
        $array['updated_at'] = $this->updated_at?->timestamp;

        return $array;
    }
}
