<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosterHome extends Model
{
    protected $table = 'poster_homes';

    protected $fillable = [
        'img',
        'name',
    ];
}
