<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroContact extends Model
{
    protected $table = 'hero_contacts';

    protected $fillable = [
        'contact_menu_id',
        'bg_img',
    ];

    public function contactMenu()
    {
        return $this->belongsTo(ContactMenu::class, 'contact_menu_id', 'id');
    }
}
