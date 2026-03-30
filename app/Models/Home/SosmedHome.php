<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosmedHome extends Model
{
    protected $table = 'sosmed_homes';

    protected $fillable = [
        'video',
        'title',
        'sosmed_link',
    ];

    public function homeMenu()
    {
        return $this->belongsTo(HomeMenu::class, 'home_menu_id', 'id');
    }
}
