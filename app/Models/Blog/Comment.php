<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['article_id', 'name', 'email', 'content'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
