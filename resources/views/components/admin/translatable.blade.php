{{--
    Input konten dua bahasa dalam tab (sama seperti form artikel):
    tab "Bahasa Indonesia" (wajib) dan tab "English" (opsional, kosong = memakai versi Indonesia).

    Props:
      key    : id unik form/modal (dipakai untuk id elemen & mengembalikan isian setelah validasi gagal)
      fields : [['name' => 'title', 'label' => 'Judul', 'type' => 'text|textarea', 'required' => true,
                 'hint' => '...', 'rows' => 4, 'max' => 255], ...]
                 -> menghasilkan input title_id & title_en
      item   : model yang sedang diubah (null untuk form tambah)
--}}
@props(['key', 'fields', 'item' => null])

@php
    // isian lama & pesan error hanya untuk form yang barusan dikirim
    $current = old('_modal') === $key;
    $value = function (string $name, string $locale) use ($current, $item) {
        $column = "{$name}_{$locale}";

        return $current ? (string) old($column, '') : (string) ($item?->{$column} ?? '');
    };
    $hasError = fn (string $locale) => $current && collect($fields)->contains(fn ($field) => $errors->has($field['name'] . '_' . $locale));
    $englishEmpty = collect($fields)->every(fn ($field) => trim($value($field['name'], 'en')) === '');
    $startWithEnglish = $hasError('en') && !$hasError('id');
    $locales = [
        'id' => ['flag' => 'id', 'label' => __('Bahasa Indonesia'), 'note' => __('Required. This is the main version shown on the website.')],
        'en' => ['flag' => 'us', 'label' => __('English'), 'note' => __('Optional. Leave empty if there is no translation yet: visitors who choose English will see the Indonesian version.')],
    ];
@endphp

<div class="mb-3">
    <ul class="nav nav-tabs lang-tabs" role="tablist">
        @foreach ($locales as $locale => $meta)
            @php $active = $startWithEnglish ? $locale === 'en' : $locale === 'id'; @endphp
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $active ? 'active' : '' }}" type="button" role="tab" data-bs-toggle="tab"
                    data-bs-target="#{{ $key }}-{{ $locale }}" aria-controls="{{ $key }}-{{ $locale }}" aria-selected="{{ $active ? 'true' : 'false' }}">
                    <img src="https://flagcdn.com/w20/{{ $meta['flag'] }}.png" width="18" alt="">
                    {{ $meta['label'] }}
                    @if ($locale === 'id')
                        <span class="badge bg-danger-soft text-danger border border-danger-subtle small">{{ __('required') }}</span>
                    @elseif ($englishEmpty)
                        <span class="badge bg-light text-secondary border small">{{ __('not filled yet') }}</span>
                    @else
                        <span class="badge bg-light text-secondary border small">{{ __('optional') }}</span>
                    @endif
                    @if ($hasError($locale))
                        <span class="error-dot" title="{{ __('There is an error in this tab') }}"></span>
                    @endif
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        @foreach ($locales as $locale => $meta)
            @php $active = $startWithEnglish ? $locale === 'en' : $locale === 'id'; @endphp
            <div class="tab-pane fade lang-panel {{ $active ? 'show active' : '' }}" id="{{ $key }}-{{ $locale }}" role="tabpanel">
                <div class="lang-note note-{{ $locale }}">
                    <i data-feather="{{ $locale === 'id' ? 'check-circle' : 'globe' }}" class="icon-xs mt-1 flex-shrink-0"></i>
                    <span>{{ $meta['note'] }}</span>
                </div>

                @foreach ($fields as $field)
                    @php
                        $column = $field['name'] . '_' . $locale;
                        $inputId = $key . '-' . $column;
                        $required = ($field['required'] ?? false) && $locale === 'id';
                        $invalid = $current && $errors->has($column);
                        $type = $field['type'] ?? 'text';
                    @endphp
                    <div class="{{ $loop->last ? '' : 'mb-3' }}">
                        <label for="{{ $inputId }}" class="form-label">
                            {{ $field['label'] }}
                            @if ($required)<span class="text-danger">*</span>@endif
                        </label>
                        @if ($type === 'textarea')
                            <textarea name="{{ $column }}" id="{{ $inputId }}" rows="{{ $field['rows'] ?? 4 }}"
                                class="form-control {{ $invalid ? 'is-invalid' : '' }}" @if (!empty($field['max'])) maxlength="{{ $field['max'] }}" @endif
                                @if ($required) required @endif>{{ $value($field['name'], $locale) }}</textarea>
                        @else
                            <input type="text" name="{{ $column }}" id="{{ $inputId }}" value="{{ $value($field['name'], $locale) }}"
                                class="form-control {{ $invalid ? 'is-invalid' : '' }}" @if (!empty($field['max'])) maxlength="{{ $field['max'] }}" @endif
                                @if ($required) required @endif>
                        @endif
                        @if ($invalid)
                            <div class="invalid-feedback">{{ $errors->first($column) }}</div>
                        @endif
                        @if (!empty($field['hint']))
                            <div class="form-text">{{ $field['hint'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
