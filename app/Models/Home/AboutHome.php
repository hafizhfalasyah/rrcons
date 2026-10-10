<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutHome extends Model
{
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

    public function getTextAttribute()
    {
        $locale = app()->getLocale();
        return $this->{'text_'.$locale};
    }

}
