<?php

namespace App\Models\RRfuture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroRRfuture extends Model
{
    protected $table = 'hero_rrfutures';

    protected $fillable = [
        'rrfuture_menu_id',
        'bg_img',
    ];

    public function rrfutureMenu()
    {
        return $this->belongsTo(RRfutureMenu::class, 'rrfuture_menu_id', 'id');
    }

}
