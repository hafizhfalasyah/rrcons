@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Blog Settings'))

@section('content')
    <x-admin.page-header :title="__('Blog Settings')" icon="sliders"
        :description="__('Old blog settings (name/value pairs) kept from the previous version of the website.')">
        <x-slot:guide>
            <li>{{ __('These values are stored but are not used by the current website pages.') }}</li>
            <li>{{ __('Contact details, texts and pictures of the website are changed in the Website content menus.') }}</li>
        </x-slot:guide>
    </x-admin.page-header>

    <div class="table-responsive">
        <table class="table table-admin table-hover align-middle border">
            <thead>
                <tr>
                    <th style="width: 30%;">{{ __('Config Name') }}</th>
                    <th>{{ __('Value') }}</th>
                    <th class="text-end" style="width: 12%;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($config as $item)
                    <tr>
                        <td><code>{{ $item->name }}</code></td>
                        <td class="text-break">{{ $item->value }}</td>
                        <td class="text-end">
                            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#edit-config-{{ $item->id }}">
                                <i data-feather="edit-2" class="icon-xs"></i> {{ __('Edit') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-state">{{ __('No data yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $config->links('pagination::bootstrap-5') }}
    </div>

    @foreach ($config as $item)
        @php
            $key = 'edit-config-' . $item->id;
            $current = old('_modal') === $key;
        @endphp
        <x-admin.form-modal :id="$key" :action="route('blog.configs.update', $item->id)" method="PUT" :title="__('Edit') . ': ' . $item->name">
            @foreach (['name' => __('Config Name'), 'value' => __('Value')] as $field => $label)
                <div class="mb-3">
                    <label for="{{ $key }}-{{ $field }}" class="form-label">{{ $label }} <span class="text-danger">*</span></label>
                    <input type="text" name="{{ $field }}" id="{{ $key }}-{{ $field }}" required minlength="3"
                        class="form-control {{ $current && $errors->has($field) ? 'is-invalid' : '' }}" value="{{ $current ? old($field) : $item->{$field} }}">
                    @if ($current && $errors->has($field))<div class="invalid-feedback">{{ $errors->first($field) }}</div>@endif
                </div>
            @endforeach
            <div class="form-text">{{ __('At least 3 characters each.') }}</div>
        </x-admin.form-modal>
    @endforeach
@endsection
