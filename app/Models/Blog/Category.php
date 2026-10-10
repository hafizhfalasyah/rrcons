<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Concerns\GeneratesUniqueSlug;

class Category extends Model
{
    use HasFactory, GeneratesUniqueSlug, HasTranslations;

    protected $translatable = ['name'];

    protected $fillable = ['name_id', 'name_en', 'slug'];

    // relasi has many category -> article
    public function Articles(): HasMany {
         return $this->hasMany(Article::class);
    }
}
