<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class PosterHome extends Model
{
    use HasTranslations;

    protected $translatable = ['name'];

    protected $table = 'poster_homes';

    protected $fillable = [
        'img',
        'name_id',
        'name_en',
    ];
}
