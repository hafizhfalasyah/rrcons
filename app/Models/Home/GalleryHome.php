<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class GalleryHome extends Model
{
    use HasTranslations;

    protected $translatable = ['title', 'desc'];

    protected $table = 'gallery_homes';

    protected $fillable = [
        'home_menu_id',
        'img',
        'title_id',
        'title_en',
        'desc_id',
        'desc_en',
        'month',
        'year',
    ];

    public function homeMenu()
    {
        return $this->belongsTo(HomeMenu::class, 'home_menu_id', 'id');
    }
}
