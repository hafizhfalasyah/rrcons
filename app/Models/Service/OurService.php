<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class OurService extends Model
{
    use HasTranslations;

    protected $translatable = ['name', 'desc'];

    protected $table = 'our_services';

    protected $fillable = [
        'service_menu_id',
        'name_id',
        'name_en',
        'desc_id',
        'desc_en',
    ];

    public function serviceMenu()
    {
        return $this->belongsTo(ServiceMenu::class, 'service_menu_id', 'id');
    }
}
