<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroService extends Model
{
    protected $table = 'hero_services';

    protected $fillable = [
        'service_menu_id',
        'bg_img',
    ];

    public function serviceMenu()
    {
        return $this->belongsTo(ServiceMenu::class, 'service_menu_id', 'id');
    }
}
