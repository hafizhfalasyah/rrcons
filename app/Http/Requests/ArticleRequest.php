<?php

namespace App\Http\Requests;

use App\Models\Blog\Article;
use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            // Bahasa Indonesia wajib, English opsional (tampilan publik fallback ke Indonesia)
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'desc_id' => 'required|string',
            'desc_en' => 'nullable|string',
            'img' => $this->imageRule(),
            // private = tidak tampil, publish = tampil sekarang, schedule = tampil otomatis pada publish_date
            'publication' => 'required|in:' . implode(',', Article::PUBLICATION_CHOICES),
            'publish_date' => 'nullable|required_if:publication,' . Article::CHOICE_SCHEDULE . '|date|after:now',
        ];
    }

    protected function imageRule(): string
    {
        return 'required|image|file|mimes:png,jpg,jpeg,webp|max:2048';
    }

    /**
     * Tanggal hanya relevan untuk opsi "schedule".
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('publication') !== Article::CHOICE_SCHEDULE) {
            $this->merge(['publish_date' => null]);
        }
    }

    public function messages(): array
    {
        return [
            'publish_date.required_if' => __('Choose the date and time when the article should be published.'),
            'publish_date.after' => __('The schedule must be in the future. Choose "Publish now" to publish immediately.'),
        ];
    }
}
