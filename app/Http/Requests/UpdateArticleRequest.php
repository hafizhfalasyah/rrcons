<?php

namespace App\Http\Requests;

class UpdateArticleRequest extends ArticleRequest
{
    /**
     * Gambar boleh tidak diganti saat edit.
     */
    protected function imageRule(): string
    {
        return 'nullable|image|file|mimes:png,jpg,jpeg,webp|max:2048';
    }
}
