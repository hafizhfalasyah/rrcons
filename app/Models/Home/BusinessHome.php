<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessHome extends Model
{
    protected $table = 'business_homes';

    protected $fillable = [
        'home_menu_id',
        'img',
        'title',
        'desc',
        'form_link',
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
