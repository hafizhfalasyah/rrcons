<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'category_id', 'title', 'slug', 'desc', 'img', 'views', 'created_by', 'status', 'publish_date'];

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

}
