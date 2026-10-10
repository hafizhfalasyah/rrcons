<?php

/*
|--------------------------------------------------------------------------
| Pesan Validasi (Bahasa Indonesia)
|--------------------------------------------------------------------------
|
| Hanya aturan yang dipakai aplikasi ini. Aturan lain otomatis memakai
| pesan bawaan Laravel (English).
|
*/

return [

    'after' => ':Attribute harus berupa tanggal setelah :date.',
    'between' => [
        'numeric' => ':Attribute harus bernilai antara :min dan :max.',
        'string' => ':Attribute harus terdiri dari :min sampai :max karakter.',
        'file' => ':Attribute harus berukuran antara :min dan :max kilobita.',
    ],
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'digits' => ':Attribute harus terdiri dari :digits digit.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'exists' => ':Attribute yang dipilih tidak valid.',
    'file' => ':Attribute harus berupa berkas.',
    'image' => ':Attribute harus berupa gambar.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'max' => [
        'numeric' => ':Attribute tidak boleh lebih dari :max.',
        'string' => ':Attribute tidak boleh lebih dari :max karakter.',
        'file' => ':Attribute tidak boleh lebih dari :max kilobita.',
        'array' => ':Attribute tidak boleh memiliki lebih dari :max item.',
    ],
    'mimes' => ':Attribute harus berupa berkas berjenis: :values.',
    'mimetypes' => ':Attribute harus berupa berkas berjenis: :values.',
    'min' => [
        'numeric' => ':Attribute minimal bernilai :min.',
        'string' => ':Attribute minimal terdiri dari :min karakter.',
        'file' => ':Attribute minimal berukuran :min kilobita.',
        'array' => ':Attribute minimal memiliki :min item.',
    ],
    'required' => ':Attribute wajib diisi.',
    'required_if' => ':Attribute wajib diisi bila :other adalah :value.',
    'required_with' => ':Attribute wajib diisi bila :values diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah digunakan.',
    'uploaded' => ':Attribute gagal diunggah.',
    'url' => ':Attribute harus berupa URL yang valid.',

    'attributes' => [
        'name' => 'nama',
        'email' => 'email',
        'password' => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
        'role' => 'role',
        'content' => 'komentar',
        'value' => 'nilai',
        'img' => 'gambar',
        'bg_img' => 'gambar latar',
        'video' => 'video',
        'month' => 'bulan',
        'year' => 'tahun',
        'form_link' => 'tautan formulir',
        'sosmed_link' => 'tautan media sosial',
        'meta_title_id' => 'judul SEO (Indonesia)',
        'meta_title_en' => 'judul SEO (English)',
        'meta_description_id' => 'meta description (Indonesia)',
        'meta_description_en' => 'meta description (English)',
        'publication' => 'status publikasi',
        'publish_date' => 'tanggal & waktu terbit',
        'category_id' => 'kategori',
        'title_id' => 'judul (Indonesia)',
        'title_en' => 'judul (English)',
        'sub_title_id' => 'subjudul (Indonesia)',
        'sub_title_en' => 'subjudul (English)',
        'name_id' => 'nama (Indonesia)',
        'name_en' => 'nama (English)',
        'desc_id' => 'deskripsi (Indonesia)',
        'desc_en' => 'deskripsi (English)',
        'text_id' => 'teks (Indonesia)',
        'text_en' => 'teks (English)',
        'position_id' => 'jabatan (Indonesia)',
        'position_en' => 'jabatan (English)',
    ],

];
