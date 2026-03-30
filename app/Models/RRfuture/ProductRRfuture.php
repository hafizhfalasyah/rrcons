<?php

namespace App\Models\RRfuture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRRfuture extends Model
{
    protected $table = 'product_rrfutures';

    protected $fillable = [
        'rrfuture_menu_id',
        'name',
        'desc',
    ];

    public function rrfutureMenu()
    {
        return $this->belongsTo(RRfutureMenu::class, 'rrfuture_menu_id', 'id');
    }

}
