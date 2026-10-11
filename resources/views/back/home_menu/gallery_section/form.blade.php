{{-- Form tambah/ubah foto galeri. Variabel: $key, $item (null = tambah), $months --}}
@php
    $current = old('_modal') === $key;
    $field = fn ($name) => $current ? old($name) : $item?->{$name};
    $invalid = fn ($name) => $current && $errors->has($name);
@endphp

<x-admin.form-modal :id="$key" :files="true"
    :action="$item ? route('home.gallery.update', $item->id) : route('home.gallery.create')"
    :title="$item ? __('Edit photo') : __('Add photo')" :icon="$item ? 'edit' : 'plus'"
    :submit="$item ? __('Save changes') : __('Add photo')">
    <div class="form-section-title">{{ __('Photo') }}</div>
    <x-admin.media-input :key="$key" name="img" :label="__('Photo')" :current="$item?->img" :required="!$item"
        accept="image/png,image/jpeg,image/webp,image/gif" :hint="__('JPG, PNG, WEBP or GIF, max 2 MB.')" />

    <div class="form-section-title">{{ __('Event date') }}</div>
    <div class="row g-3 mb-3">
        <div class="col-sm-7">
            <label for="{{ $key }}-month" class="form-label">{{ __('Month') }} <span class="text-danger">*</span></label>
            <select name="month" id="{{ $key }}-month" class="form-select {{ $invalid('month') ? 'is-invalid' : '' }}" required>
                <option value="">{{ __('Choose month') }}</option>
                @foreach ($months as $number => $name)
                    <option value="{{ $number }}" @selected((int) $field('month') === $number)>{{ $name }}</option>
                @endforeach
            </select>
            @if ($invalid('month'))<div class="invalid-feedback">{{ $errors->first('month') }}</div>@endif
        </div>
        <div class="col-sm-5">
            <label for="{{ $key }}-year" class="form-label">{{ __('Year') }} <span class="text-danger">*</span></label>
            <input type="number" name="year" id="{{ $key }}-year" min="1900" max="2100" required
                class="form-control {{ $invalid('year') ? 'is-invalid' : '' }}" value="{{ $field('year') ?? now()->year }}">
            @if ($invalid('year'))<div class="invalid-feedback">{{ $errors->first('year') }}</div>@endif
        </div>
        <div class="col-12"><div class="form-text mt-0">{{ __('Used by visitors to filter the gallery.') }}</div></div>
    </div>

    <div class="form-section-title">{{ __('Text') }}</div>
    <x-admin.translatable :key="$key" :item="$item" :fields="[
        ['name' => 'title', 'label' => __('Event name'), 'required' => true, 'max' => 255],
        ['name' => 'desc', 'label' => __('Short description'), 'type' => 'textarea', 'rows' => 3, 'max' => 255, 'hint' => __('Optional, max 255 characters.')],
    ]" />
</x-admin.form-modal>
