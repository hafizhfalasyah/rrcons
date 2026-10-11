@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Team Profiles'))

@section('content')
    <x-admin.page-header :title="__('Team Profiles') . ' — ' . __('Home Page')" icon="users"
        :description="__('Photos, names and positions of the RR Consultant team on the home page.')"
        :location="__('Home Page') . ' — ' . __('Team Profiles')" :preview-url="url('/')">
        <x-slot:guide>
            <li>{{ __('The name is written the same in both languages; only the position is translated.') }}</li>
            <li>{{ __('Photo: portrait (standing) orientation works best, JPG, PNG or WEBP, max 2 MB.') }}</li>
        </x-slot:guide>
    </x-admin.page-header>

    <div class="row g-3">
        @forelse ($profiles as $item)
            <div class="col-sm-6 col-lg-4 col-xxl-3">
                <div class="panel d-flex flex-column">
                    @if ($item->img)
                        <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}" class="w-100" style="height: 220px; object-fit: cover; border-radius: 1rem 1rem 0 0;">
                    @else
                        <div class="empty-state" style="height: 220px;">{{ __('No photo yet') }}</div>
                    @endif
                    <div class="panel-body d-flex flex-column flex-grow-1">
                        <div class="fw-semibold">{{ $item->name }}</div>
                        <div class="small mt-1"><x-admin.bilingual :id="$item->position_id" :en="$item->position_en" :limit="60" /></div>
                        <div class="d-flex justify-content-between align-items-end mt-auto pt-3">
                            <div class="small text-muted"><x-admin.updated-at :value="$item->updated_at" /></div>
                            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#edit-profile-{{ $item->id }}">
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

    @foreach ($profiles as $item)
        @php $key = 'edit-profile-' . $item->id; @endphp
        <x-admin.form-modal :id="$key" :action="route('home.profile.update', $item->id)" :title="__('Edit') . ': ' . $item->name" :files="true">
            <div class="form-section-title">{{ __('Name') }}</div>
            <div class="mb-3">
                <label for="{{ $key }}-name" class="form-label">{{ __('Full name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="{{ $key }}-name" maxlength="255" required
                    class="form-control {{ old('_modal') === $key && $errors->has('name') ? 'is-invalid' : '' }}"
                    value="{{ old('_modal') === $key ? old('name') : $item->name }}">
                @if (old('_modal') === $key && $errors->has('name'))
                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                @endif
                <div class="form-text">{{ __('Written the same in both languages, including academic titles if any.') }}</div>
            </div>

            <div class="form-section-title">{{ __('Position') }}</div>
            <x-admin.translatable :key="$key" :item="$item" :fields="[
                ['name' => 'position', 'label' => __('Position'), 'max' => 255, 'hint' => __('For example: Director, Senior Consultant.')],
            ]" />

            <div class="form-section-title">{{ __('Photo') }}</div>
            <x-admin.media-input :key="$key" name="img" :label="__('Photo')" :current="$item->img"
                accept="image/png,image/jpeg,image/webp,image/gif" :hint="__('JPG, PNG, WEBP or GIF, max 2 MB.')" />
        </x-admin.form-modal>
    @endforeach
@endsection
