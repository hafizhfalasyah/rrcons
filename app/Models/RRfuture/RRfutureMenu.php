<?php

namespace App\Models\RRfuture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RRfutureMenu extends Model
{
    protected $table = 'rrfuture_menus';

    protected $fillable = [
        'title',
        'sub_title',
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
