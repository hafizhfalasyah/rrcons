<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceHome extends Model
{
    protected $table = 'service_homes';

    protected $fillable = [
        'home_menu_id',
        'name_id',
        'name_en',
        'desc_id',
        'desc_en',
    ];

    public function homeMenu()
    {
        return $this->belongsTo(HomeMenu::class, 'home_menu_id', 'id');
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $this->{'name_'.$locale};
    }

    public function getDescAttribute()
    {
        $locale = app()->getLocale();
        return $this->{'desc_'.$locale};
    }

}
