<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class AboutHome extends Model
{
    use HasTranslations;

    protected $translatable = ['text'];

    protected $table = 'about_homes';

    protected $fillable = [
        'home_menu_id',
        'img',
        'text_id',
        'text_en',
    ];

    public function homeMenu()
    {
        return $this->belongsTo(HomeMenu::class, 'home_menu_id', 'id');
    }

}
