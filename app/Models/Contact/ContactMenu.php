<?php

namespace App\Models\Contact;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasTranslations;

class ContactMenu extends Model
{
    use HasTranslations;

    protected $translatable = ['title', 'sub_title'];

    protected $table = 'contact_menus';

    protected $fillable = [
        'title_id',
        'title_en',
        'sub_title_id',
        'sub_title_en',
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
