@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('About Us'))

@section('content')
    <x-admin.page-header :title="__('About Us') . ' — ' . __('Home Page')" icon="info"
        :description="__('The picture and the introduction text about RR Consultant on the home page.')"
        :location="__('Home Page') . ' — ' . __('About Us')" :preview-url="url('/')">
        <x-slot:guide>
            <li>{{ __('Write the introduction in paragraphs; line breaks are kept on the website.') }}</li>
            <li>{{ __('Picture: JPG, PNG or WEBP, max 2 MB. A portrait or square photo looks best.') }}</li>
            <li>{!! __('The heading above this section is changed in :link.', ['link' => '<a href="' . e(route('home.menu')) . '">' . e(__('Section Headings')) . '</a>']) !!}</li>
        </x-slot:guide>
    </x-admin.page-header>

    @forelse ($abouts as $item)
        <div class="panel mb-3">
            <div class="panel-body">
                <div class="row g-4 align-items-start">
                    <div class="col-md-4 col-lg-3">
                        @if ($item->img)
                            <img src="{{ asset('storage/' . $item->img) }}" alt="{{ __('About Us') }}" class="w-100 rounded-3 border" style="max-height: 260px; object-fit: cover;">
                        @else
                            <div class="empty-state border rounded-3">{{ __('No image yet') }}</div>
                        @endif
                    </div>
                    <div class="col-md-8 col-lg-9">
                        <x-admin.bilingual :id="$item->text_id" :en="$item->text_en" :limit="420" />
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                            <div class="small text-muted">{{ __('Last Updated') }}: <x-admin.updated-at :value="$item->updated_at" /></div>
                            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#edit-about-{{ $item->id }}">
                                <i data-feather="edit-2" class="icon-xs"></i> {{ __('Edit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state border rounded-3">{{ __('No data yet.') }}</div>
    @endforelse

    @foreach ($abouts as $item)
        <x-admin.form-modal :id="'edit-about-' . $item->id" :action="route('home.about.update', $item->id)" :title="__('Edit') . ': ' . __('About Us')" :files="true">
            <div class="form-section-title">{{ __('Text') }}</div>
            <x-admin.translatable :key="'edit-about-' . $item->id" :item="$item" :fields="[
                ['name' => 'text', 'label' => __('Introduction text'), 'type' => 'textarea', 'rows' => 8, 'required' => true, 'hint' => __('Press Enter to start a new paragraph.')],
            ]" />

            <div class="form-section-title">{{ __('Picture') }}</div>
            <x-admin.media-input :key="'edit-about-' . $item->id" name="img" :label="__('Picture')" :current="$item->img"
                accept="image/png,image/jpeg,image/webp,image/gif" :hint="__('JPG, PNG, WEBP or GIF, max 2 MB.')" />
        </x-admin.form-modal>
    @endforeach
@endsection
