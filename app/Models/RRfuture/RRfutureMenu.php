<?php

namespace App\Models\RRfuture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class RRfutureMenu extends Model
{
    use HasTranslations;

    protected $translatable = ['title', 'sub_title'];

    protected $table = 'rrfuture_menus';

    protected $fillable = [
        'title_id',
        'title_en',
        'sub_title_id',
        'sub_title_en',
    ];

    public function heroSections()
    {
        return $this->hasMany(HeroRRfuture::class, 'rrfuture_menu_id', 'id');
    }

    public function productSections()
    {
        return $this->hasMany(ProductRRfuture::class, 'rrfuture_menu_id', 'id');
    }

}
