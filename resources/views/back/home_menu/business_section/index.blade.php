@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Business Training'))

@section('content')
    <x-admin.page-header :title="__('Business Training') . ' — ' . __('Home Page')" icon="book-open"
        :description="__('Training programs offered on the home page, each with a picture and a registration link.')"
        :location="__('Home Page') . ' — ' . __('Business Training')" :preview-url="url('/')">
        <x-slot:guide>
            <li>{{ __('The registration link is opened when visitors click the button, e.g. a Google Form link.') }}</li>
            <li>{{ __('Keep the description short (max 255 characters).') }}</li>
            <li>{!! __('The heading above this section is changed in :link.', ['link' => '<a href="' . e(route('home.menu')) . '">' . e(__('Section Headings')) . '</a>']) !!}</li>
        </x-slot:guide>
    </x-admin.page-header>

    <div class="table-responsive">
        <table class="table table-admin table-hover align-middle border">
            <thead>
                <tr>
                    <th style="width: 12%;">{{ __('Picture') }}</th>
                    <th>{{ __('Training') }}</th>
                    <th style="width: 22%;">{{ __('Registration link') }}</th>
                    <th style="width: 13%;">{{ __('Last Updated') }}</th>
                    <th class="text-end" style="width: 10%;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($business as $item)
                    <tr>
                        <td>
                            @if ($item->img)
                                <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->title }}" class="thumb">
                            @else
                                <span class="text-muted small">{{ __('No image yet') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold mb-1"><x-admin.bilingual :id="$item->title_id" :en="$item->title_en" :limit="70" /></div>
                            <div class="small"><x-admin.bilingual :id="$item->desc_id" :en="$item->desc_en" :limit="120" /></div>
                        </td>
                        <td class="small text-break">
                            @if ($item->form_link)
                                <a href="{{ $item->form_link }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($item->form_link, 45) }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="small"><x-admin.updated-at :value="$item->updated_at" /></td>
                        <td class="text-end">
                            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#edit-business-{{ $item->id }}">
                                <i data-feather="edit-2" class="icon-xs"></i> {{ __('Edit') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">{{ __('No data yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @foreach ($business as $item)
        @php $key = 'edit-business-' . $item->id; @endphp
        <x-admin.form-modal :id="$key" :action="route('home.business.update', $item->id)" :title="__('Edit') . ': ' . \Illuminate\Support\Str::limit((string) $item->title_id, 50)" :files="true">
            <div class="form-section-title">{{ __('Text') }}</div>
            <x-admin.translatable :key="$key" :item="$item" :fields="[
                ['name' => 'title', 'label' => __('Training name'), 'required' => true, 'max' => 255],
                ['name' => 'desc', 'label' => __('Short description'), 'type' => 'textarea', 'rows' => 3, 'required' => true, 'max' => 255, 'hint' => __('Max 255 characters.')],
            ]" />

            <div class="form-section-title">{{ __('Registration link') }}</div>
            <div class="mb-3">
                <label for="{{ $key }}-form_link" class="form-label">{{ __('Registration link (URL)') }} <span class="text-danger">*</span></label>
                <input type="url" name="form_link" id="{{ $key }}-form_link" maxlength="255" required placeholder="https://"
                    class="form-control {{ old('_modal') === $key && $errors->has('form_link') ? 'is-invalid' : '' }}"
                    value="{{ old('_modal') === $key ? old('form_link') : $item->form_link }}">
                @if (old('_modal') === $key && $errors->has('form_link'))
                    <div class="invalid-feedback">{{ $errors->first('form_link') }}</div>
                @endif
                <div class="form-text">{{ __('Must start with https:// — e.g. a Google Form or WhatsApp link.') }}</div>
            </div>

            <div class="form-section-title">{{ __('Picture') }}</div>
            <x-admin.media-input :key="$key" name="img" :label="__('Picture')" :current="$item->img"
                accept="image/png,image/jpeg,image/webp,image/gif" :hint="__('JPG, PNG, WEBP or GIF, max 2 MB.')" />
        </x-admin.form-modal>
    @endforeach
@endsection
