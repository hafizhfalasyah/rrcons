<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class ProfileHome extends Model
{
    use HasTranslations;

    protected $translatable = ['position'];

    protected $fillable = [
        'home_menu_id',
        'img',
        'name',
        'position_id',
        'position_en',
    ];

    public function homeMenu()
    {
        return $this->belongsTo(HomeMenu::class, 'home_menu_id', 'id');
    }

}
