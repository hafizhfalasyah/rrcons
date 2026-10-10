<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Concerns\GeneratesUniqueSlug;
use App\Models\User;
use App\Support\HtmlSanitizer;
use Illuminate\Support\Carbon;

class Article extends Model
{
    use HasFactory, GeneratesUniqueSlug, HasTranslations;

    protected $translatable = ['title', 'desc'];

    // Nilai kolom status
    const STATUS_PRIVATE = '0';
    const STATUS_PUBLISH = '1';

    // Status publikasi hasil kombinasi status + publish_date
    const PUBLICATION_PRIVATE = 'private';
    const PUBLICATION_SCHEDULED = 'scheduled';
    const PUBLICATION_PUBLISHED = 'published';

    // Pilihan di form admin
    const CHOICE_PRIVATE = 'private';
    const CHOICE_PUBLISH = 'publish';
    const CHOICE_SCHEDULE = 'schedule';
    const PUBLICATION_CHOICES = [self::CHOICE_PRIVATE, self::CHOICE_PUBLISH, self::CHOICE_SCHEDULE];

    protected $fillable = ['user_id', 'category_id', 'title_id', 'title_en', 'slug', 'desc_id', 'desc_en', 'img', 'views', 'created_by', 'status', 'publish_date'];

    protected $casts = [
        'publish_date' => 'datetime',
    ];

    // /blog/articles, /blog/categories, /blog/configs dipakai route admin
    protected static function reservedSlugs(): array
    {
        return ['articles', 'categories', 'configs'];
    }

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

    /**
     * Opsi yang terpilih di form untuk artikel ini.
     */
    public function getPublicationChoiceAttribute(): string
    {
        return match ($this->publication_status) {
            self::PUBLICATION_PRIVATE => self::CHOICE_PRIVATE,
            self::PUBLICATION_SCHEDULED => self::CHOICE_SCHEDULE,
            default => self::CHOICE_PUBLISH,
        };
    }

    /**
     * Terjemahkan pilihan form (private / publish / schedule) menjadi kolom status & publish_date.
     */
    public static function publicationAttributes(string $choice, $scheduleAt = null, ?self $current = null): array
    {
        return match ($choice) {
            self::CHOICE_PRIVATE => [
                'status' => self::STATUS_PRIVATE,
                'publish_date' => $current?->publish_date,
            ],
            self::CHOICE_SCHEDULE => [
                'status' => self::STATUS_PUBLISH,
                'publish_date' => Carbon::parse($scheduleAt),
            ],
            // Artikel yang sudah tayang mempertahankan tanggal terbitnya agar urutan tidak berubah
            default => [
                'status' => self::STATUS_PUBLISH,
                'publish_date' => $current?->publication_status === self::PUBLICATION_PUBLISHED && $current->publish_date
                    ? $current->publish_date
                    : now(),
            ],
        };
    }

    // cari judul di kedua bahasa
    public function scopeSearchTitle(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title_id', 'like', "%{$term}%")
                ->orWhere('title_en', 'like', "%{$term}%");
        });
    }

    /**
     * Isi artikel (HTML dari editor) yang sudah dibersihkan dari script / atribut berbahaya.
     */
    public function getSafeDescAttribute(): string
    {
        return HtmlSanitizer::clean($this->desc);
    }
}
