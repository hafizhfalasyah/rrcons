<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvantageHome extends Model
{
    protected $table = 'advantage_homes';

    protected $fillable = [
        'home_menu_id',
        'title',
        'desc',
    ];

    public function homeMenu()
    {
        return $this->belongsTo(HomeMenu::class, 'home_menu_id', 'id');
    }

    public function getTitleAttribute()
    {
        $locale = app()->getLocale();
        return $this->{'title_'.$locale};
    }

    public function getDescAttribute()
    {
        $locale = app()->getLocale();
        return $this->{'desc_'.$locale};
    }

}
