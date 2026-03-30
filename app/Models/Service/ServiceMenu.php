<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceMenu extends Model
{
    protected $table = 'service_menus';

    protected $fillable = [
        'title',
        'sub_title',
    ];

    public function heroSections()
    {
        return $this->hasMany(HeroService::class, 'service_menu_id', 'id');
    }

    public function serviceSections()
    {
        return $this->hasMany(OurService::class, 'service_menu_id', 'id');
    }
}
