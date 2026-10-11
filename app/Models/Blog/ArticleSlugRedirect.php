<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Slug lama sebuah artikel; /blog/{slug-lama} dialihkan (301) ke slug terbaru.
 */
class ArticleSlugRedirect extends Model
{
    protected $fillable = ['article_id', 'slug'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
