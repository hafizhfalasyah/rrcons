<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsContact extends Model
{
    protected $table = 'us_contacts';

    protected $fillable = [
        'contact_menu_id',
        'title',
        'desc',
    ];

    public function contactMenu()
    {
        return $this->belongsTo(ContactMenu::class, 'contact_menu_id', 'id');
    }
}
