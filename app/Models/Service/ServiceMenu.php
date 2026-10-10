<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class ServiceMenu extends Model
{
    use HasTranslations;

    protected $translatable = ['title', 'sub_title'];

    protected $table = 'service_menus';

    protected $fillable = [
        'title_id',
        'title_en',
        'sub_title_id',
        'sub_title_en',
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
