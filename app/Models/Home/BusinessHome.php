<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class BusinessHome extends Model
{
    use HasTranslations;

    protected $translatable = ['title', 'desc'];

    protected $table = 'business_homes';

    protected $fillable = [
        'home_menu_id',
        'img',
        'title_id',
        'title_en',
        'desc_id',
        'desc_en',
        'form_link',
    ];

    public function homeMenu()
    {
        return $this->belongsTo(HomeMenu::class, 'home_menu_id', 'id');
    }
}
