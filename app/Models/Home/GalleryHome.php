<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryHome extends Model
{
    protected $table = 'gallery_homes';

    protected $fillable = [
        'home_menu_id',
        'img',
        'title',
        'desc',
        'month',
        'year',
    ];

    public function homeMenu()
    {
        return $this->belongsTo(HomeMenu::class, 'home_menu_id', 'id');
    }
}
