<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;

class Article extends Model
{
    use HasFactory;

    // Nilai kolom status
    const STATUS_PRIVATE = '0';
    const STATUS_PUBLISH = '1';

    // Status publikasi hasil kombinasi status + publish_date
    const PUBLICATION_PRIVATE = 'private';
    const PUBLICATION_SCHEDULED = 'scheduled';
    const PUBLICATION_PUBLISHED = 'published';

    protected $fillable = ['user_id', 'category_id', 'title', 'slug', 'desc', 'img', 'views', 'created_by', 'status', 'publish_date'];

    protected $casts = [
        'publish_date' => 'datetime',
    ];

    //relasi ke user
    public function User():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function Creator():BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // relasi ke kategori
    public function Category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // relasi ke comment
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // hanya artikel berstatus Publish yang waktu terbitnya sudah tiba
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISH)
            ->where(function (Builder $q) {
                $q->whereNull('publish_date')
                    ->orWhere('publish_date', '<=', now());
            });
    }

    public function getPublicationStatusAttribute(): string
    {
        if ((string) $this->status !== self::STATUS_PUBLISH) {
            return self::PUBLICATION_PRIVATE;
        }

        if ($this->publish_date && $this->publish_date->isFuture()) {
            return self::PUBLICATION_SCHEDULED;
        }

        return self::PUBLICATION_PUBLISHED;
    }

}
