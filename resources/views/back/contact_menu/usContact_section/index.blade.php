@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Contact Details'))

@php
    $icons = ['Alamat' => 'map-pin', 'Telepon' => 'phone', 'Email' => 'mail'];
    $labels = ['Alamat' => __('Address'), 'Telepon' => __('Phone'), 'Email' => __('Email')];
@endphp

@section('content')
    <x-admin.page-header :title="__('Contact Details') . ' — ' . __('Contact Page')" icon="phone"
        :description="__('The address, phone number and email shown on the contact page.')"
        :location="__('Contact Page') . ' — ' . __('Contact Details')" :preview-url="route('contact')">
        <x-slot:guide>
            <li>{{ __('The type of each item (Address, Phone, Email) is fixed; only its content can be changed.') }}</li>
            <li>{{ __('The address is also used by search engines (Google) to show the company location.') }}</li>
            <li>{{ __('Max 255 characters per item.') }}</li>
        </x-slot:guide>
    </x-admin.page-header>

    <div class="row g-3">
        @forelse ($us as $item)
            <div class="col-md-6 col-xl-4">
                <div class="panel d-flex flex-column">
                    <div class="panel-header">
                        <span class="d-flex align-items-center gap-2">
                            <i data-feather="{{ $icons[$item->title] ?? 'info' }}" class="icon-sm text-primary"></i> {{ $labels[$item->title] ?? $item->title }}
                        </span>
                    </div>
                    <div class="panel-body d-flex flex-column flex-grow-1">
                        <div class="small" style="white-space: pre-line;"><x-admin.bilingual :id="$item->desc_id" :en="$item->desc_en" :limit="200" /></div>
                        <div class="d-flex justify-content-between align-items-end mt-auto pt-3">
                            <div class="small text-muted"><x-admin.updated-at :value="$item->updated_at" /></div>
                            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#edit-contact-{{ $item->id }}">
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

    @foreach ($us as $item)
        <x-admin.form-modal :id="'edit-contact-' . $item->id" :action="route('contact.usContact.update', $item->id)"
            :title="__('Edit') . ': ' . ($labels[$item->title] ?? $item->title)">
            <x-admin.translatable :key="'edit-contact-' . $item->id" :item="$item" :fields="[
                ['name' => 'desc', 'label' => $labels[$item->title] ?? $item->title, 'type' => 'textarea', 'rows' => 3, 'required' => true, 'max' => 255,
                 'hint' => __('Phone numbers and emails are usually the same in both languages; addresses may be translated.')],
            ]" />
        </x-admin.form-modal>
    @endforeach
@endsection
