<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog\Config;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            [
                'name' => 'logo',
                'value' => 'logo.png'
            ],
            [
                'name' => 'blogname',
                'value' => 'rrcons'
            ],
            [
                'name' => 'title',
                'value' => 'Welcome to Blog Home!'
            ],
            [
                'name' => 'caption',
                'value' => 'A Bootstrap 5 starter layout for your next blog homepage'
            ],
            [
                'name' => 'ads_widget',
                'value' => 'adsense 1'
            ],
            [
                'name' => 'ads_header',
                'value' => 'adsense 2'
            ],
            [
                'name' => 'ads_footer',
                'value' => 'adsense 3'
            ],
            [
                'name' => 'phone',
                'value' => '102310992'
            ],
            [
                'name' => 'email',
                'value' => 'rrcons@gmail.com'
            ],
            [
                'name' => 'facebook',
                'value' => 'facebook.com'
            ],
            [
                'name' => 'instagram',
                'value' => 'instagram.com'
            ],
            [
                'name' => 'youtube',
                'value' => 'youtube.com'
            ],
            [
                'name' => 'footer',
                'value' => 'rrcons'
            ],
        ];

        // Aman dijalankan ulang: hanya menambah config yang belum ada, nilai yang sudah diubah admin tidak ditimpa
        foreach ($defaults as $config) {
            Config::firstOrCreate(['name' => $config['name']], ['value' => $config['value']]);
        }
    }
}
