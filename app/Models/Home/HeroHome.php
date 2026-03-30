<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroHome extends Model
{
    protected $table = 'hero_homes';

    protected $fillable = [
        'home_menu_id',
        'bg_img',
    ];

    public function homeMenu()
    {
        return $this->belongsTo(HomeMenu::class, 'home_menu_id', 'id');
    }

}
