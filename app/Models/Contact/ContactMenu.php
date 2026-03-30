<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMenu extends Model
{
    protected $table = 'contact_menus';

    protected $fillable = [
        'title',
        'sub_title',
    ];

    public function heroSections()
    {
        return $this->hasMany(HeroContact::class, 'contact_menu_id', 'id');
    }

    public function contactSections()
    {
        return $this->hasMany(UsContact::class, 'contact_menu_id', 'id');
    }
}
