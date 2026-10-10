<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class ServiceHome extends Model
{
    use HasTranslations;

    protected $translatable = ['name', 'desc'];

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

}
