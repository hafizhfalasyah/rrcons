<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class UsContact extends Model
{
    use HasTranslations;

    protected $translatable = ['desc'];

    protected $table = 'us_contacts';

    protected $fillable = [
        'contact_menu_id',
        'title',
        'desc_id',
        'desc_en',
    ];

    public function contactMenu()
    {
        return $this->belongsTo(ContactMenu::class, 'contact_menu_id', 'id');
    }
}
