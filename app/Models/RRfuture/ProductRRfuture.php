<?php

namespace App\Models\RRfuture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class ProductRRfuture extends Model
{
    use HasTranslations;

    protected $translatable = ['name', 'desc'];

    protected $table = 'product_rrfutures';

    protected $fillable = [
        'rrfuture_menu_id',
        'name_id',
        'name_en',
        'desc_id',
        'desc_en',
    ];

    public function rrfutureMenu()
    {
        return $this->belongsTo(RRfutureMenu::class, 'rrfuture_menu_id', 'id');
    }

}
