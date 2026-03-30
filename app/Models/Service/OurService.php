<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurService extends Model
{
    protected $table = 'our_services';

    protected $fillable = [
        'service_menu_id',
        'name',
        'desc',
    ];

    public function serviceMenu()
    {
        return $this->belongsTo(ServiceMenu::class, 'service_menu_id', 'id');
    }
}
