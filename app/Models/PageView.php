<?php

namespace App\Models;

use App\Models\Blog\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    public const UPDATED_AT = null;

    public const TRACKER_JS = 'js';
    public const TRACKER_LEGACY = 'legacy';

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'engaged_seconds' => 'integer',
        'screen_width' => 'integer',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function scopeBetween(Builder $query, $from, $to): Builder
    {
        return $query->where('created_at', '>=', $from)->where('created_at', '<=', $to);
    }

    /**
     * Pengunjung yang masih aktif (heartbeat terakhir dalam jendela "online").
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('last_seen_at', '>=', now()->subSeconds(config('analytics.active_window')));
    }
}
