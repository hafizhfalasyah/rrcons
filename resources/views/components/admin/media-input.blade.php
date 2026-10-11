{{--
    Unggah gambar/video dengan pratinjau file saat ini & file baru.
    Props : key, name, label, current (path di disk public), type (image|video), required, hint, accept
--}}
@props([
    'key',
    'name',
    'label',
    'current' => null,
    'type' => 'image',
    'required' => false,
    'hint' => null,
    'accept' => null,
])

@php
    $inputId = $key . '-' . $name;
    $invalid = old('_modal') === $key && $errors->has($name);
    $accept ??= $type === 'video' ? 'video/mp4,video/mpeg,video/x-matroska,video/quicktime' : 'image/png,image/jpeg,image/webp';
@endphp

<div class="mb-3">
    <label for="{{ $inputId }}" class="form-label">
        {{ $label }} @if ($required)<span class="text-danger">*</span>@endif
    </label>

    <div class="media-preview mb-2" id="{{ $inputId }}-preview">
        @if ($current)
            @if ($type === 'video')
                <video src="{{ asset('storage/' . $current) }}" controls muted preload="metadata"></video>
            @else
                <img src="{{ asset('storage/' . $current) }}" alt="{{ $label }}">
            @endif
            <div class="small text-muted mt-1">{{ __('Current file') }}</div>
        @else
            <div class="small text-muted py-3">{{ $type === 'video' ? __('No video yet') : __('No image yet') }}</div>
        @endif
    </div>

    <input type="file" name="{{ $name }}" id="{{ $inputId }}" accept="{{ $accept }}" data-preview="{{ $inputId }}-preview"
        class="form-control {{ $invalid ? 'is-invalid' : '' }}" @if ($required) required @endif>
    @if ($invalid)
        <div class="invalid-feedback">{{ $errors->first($name) }}</div>
    @endif
    <div class="form-text">
        @if ($current && !$required)
            {{ __('Leave empty to keep the current file.') }}
        @endif
        {{ $hint }}
    </div>
</div>
