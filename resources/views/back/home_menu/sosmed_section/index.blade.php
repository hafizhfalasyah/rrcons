@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Social Media Videos'))

@section('content')
    <x-admin.page-header :title="__('Social Media Videos') . ' — ' . __('Home Page')" icon="video"
        :description="__('Short videos from RR Consultant social media posts, shown on the home page.')"
        :location="__('Home Page') . ' — ' . __('Social Media Videos')" :preview-url="url('/')">
        <x-slot:guide>
            <li>{{ __('Video: MP4 (recommended), MOV, MKV or MPEG, max 50 MB. Short vertical videos work best.') }}</li>
            <li>{{ __('The social media link opens the original post (Instagram, TikTok, YouTube, …).') }}</li>
            <li>{!! __('The heading above this section is changed in :link.', ['link' => '<a href="' . e(route('home.menu')) . '">' . e(__('Section Headings')) . '</a>']) !!}</li>
        </x-slot:guide>
    </x-admin.page-header>

    <div class="row g-3">
        @forelse ($sosmed as $item)
            <div class="col-md-6 col-xl-4">
                <div class="panel d-flex flex-column">
                    <div class="panel-body d-flex flex-column flex-grow-1">
                        @if ($item->video)
                            <video src="{{ asset('storage/' . $item->video) }}" controls muted preload="metadata" class="w-100 rounded-3 bg-dark" style="max-height: 260px;"></video>
                        @else
                            <div class="empty-state border rounded-3">{{ __('No video yet') }}</div>
                        @endif
                        <div class="fw-semibold mt-3"><x-admin.bilingual :id="$item->title_id" :en="$item->title_en" :limit="70" /></div>
                        <div class="small text-break mt-2">
                            <i data-feather="link" class="icon-xs me-1"></i>
                            @if ($item->sosmed_link)
                                <a href="{{ $item->sosmed_link }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($item->sosmed_link, 45) }}</a>
                            @else
                                <span class="text-muted">{{ __('No link') }}</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-end mt-auto pt-3">
                            <div class="small text-muted"><x-admin.updated-at :value="$item->updated_at" /></div>
                            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#edit-sosmed-{{ $item->id }}">
                                <i data-feather="edit-2" class="icon-xs"></i> {{ __('Edit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="empty-state border rounded-3">{{ __('No data yet.') }}</div></div>
        @endforelse
    </div>

    @foreach ($sosmed as $item)
        @php $key = 'edit-sosmed-' . $item->id; @endphp
        <x-admin.form-modal :id="$key" :action="route('home.sosmed.update', $item->id)" :title="__('Edit') . ': ' . \Illuminate\Support\Str::limit((string) $item->title_id, 50)" :files="true">
            <div class="form-section-title">{{ __('Caption') }}</div>
            <x-admin.translatable :key="$key" :item="$item" :fields="[
                ['name' => 'title', 'label' => __('Video caption'), 'required' => true, 'max' => 255],
            ]" />

            <div class="form-section-title">{{ __('Social media link') }}</div>
            <div class="mb-3">
                <label for="{{ $key }}-sosmed_link" class="form-label">{{ __('Link to the post (URL)') }}</label>
                <input type="url" name="sosmed_link" id="{{ $key }}-sosmed_link" maxlength="255" placeholder="https://"
                    class="form-control {{ old('_modal') === $key && $errors->has('sosmed_link') ? 'is-invalid' : '' }}"
                    value="{{ old('_modal') === $key ? old('sosmed_link') : $item->sosmed_link }}">
                @if (old('_modal') === $key && $errors->has('sosmed_link'))
                    <div class="invalid-feedback">{{ $errors->first('sosmed_link') }}</div>
                @endif
                <div class="form-text">{{ __('Optional. Must start with https://') }}</div>
            </div>

            <div class="form-section-title">{{ __('Video') }}</div>
            <x-admin.media-input :key="$key" name="video" :label="__('Video file')" :current="$item->video" type="video"
                :hint="__('MP4, MOV, MKV or MPEG, max 50 MB. Uploading a large video can take a while — keep this window open.')" />
        </x-admin.form-modal>
    @endforeach
@endsection
