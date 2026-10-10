<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class HomeMenu extends Model
{
    use HasTranslations;

    protected $translatable = ['title', 'sub_title'];

    protected $table = 'home_menus';

    protected $fillable = [
        'title_id',
        'title_en',
        'sub_title_id',
        'sub_title_en',
    ];

    public function heroSections()
    {
        return $this->hasMany(HeroHome::class, 'home_menu_id', 'id');
    }

    public function aboutSections()
    {
        return $this->hasMany(AboutHome::class, 'home_menu_id', 'id');
    }

    public function serviceSections()
    {
        return $this->hasMany(ServiceHome::class, 'home_menu_id', 'id');
    }

    public function businessSections()
    {
        return $this->hasMany(BusinessHome::class, 'home_menu_id', 'id');
    }

    public function sosmedSections()
    {
        return $this->hasMany(SosmedHome::class, 'home_menu_id', 'id');
    }

    public function advantageSections()
    {
        return $this->hasMany(AdvantageHome::class, 'home_menu_id', 'id');
    }

    public function profileSections()
    {
        return $this->hasMany(ProfileHome::class, 'home_menu_id', 'id');
    }

    public function gallerySections()
    {
        return $this->hasMany(GalleryHome::class, 'home_menu_id', 'id');
    }

}
