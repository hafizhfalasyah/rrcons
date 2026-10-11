@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Pop-up Poster'))

@section('content')
    <x-admin.page-header :title="__('Pop-up Poster') . ' — ' . __('Home Page')" icon="maximize"
        :description="__('Posters that pop up when visitors open the home page, e.g. promotions or event announcements.')"
        :location="__('Home Page') . ' — ' . __('pop-up window when the page opens')" :preview-url="url('/')">
        <x-slot:actions>
            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#create-poster">
                <i data-feather="plus" class="icon-xs"></i> {{ __('Add poster') }}
            </button>
        </x-slot:actions>
        <x-slot:guide>
            <li>{{ __('If there is more than one poster, they slide automatically. Delete posters that are no longer relevant.') }}</li>
            <li>{{ __('Posters without a picture are not shown on the website.') }}</li>
            <li>{{ __('Picture: JPG, PNG or WEBP, max 2 MB. Portrait or square works best.') }}</li>
        </x-slot:guide>
    </x-admin.page-header>

    <div class="row g-3">
        @forelse ($posters as $item)
            <div class="col-sm-6 col-lg-4 col-xxl-3">
                <div class="panel d-flex flex-column">
                    @if ($item->img)
                        <img src="{{ asset('storage/' . $item->img) }}" alt="{{ $item->name }}" class="w-100" style="height: 240px; object-fit: contain; background: #f8fafc; border-radius: 1rem 1rem 0 0;">
                    @else
                        <div class="empty-state" style="height: 240px;">
                            <span class="badge bg-warning text-dark">{{ __('Not shown: no picture') }}</span>
                        </div>
                    @endif
                    <div class="panel-body d-flex flex-column flex-grow-1">
                        <div class="fw-semibold small"><x-admin.bilingual :id="$item->name_id" :en="$item->name_en" :limit="60" /></div>
                        <div class="small text-muted mt-2"><x-admin.updated-at :value="$item->updated_at" /></div>
                        <div class="d-flex gap-2 mt-auto pt-3">
                            <button class="btn btn-primary btn-sm flex-grow-1 d-inline-flex align-items-center justify-content-center gap-1" data-bs-toggle="modal" data-bs-target="#edit-poster-{{ $item->id }}">
                                <i data-feather="edit-2" class="icon-xs"></i> {{ __('Edit') }}
                            </button>
                            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete-poster-{{ $item->id }}" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                <i data-feather="trash-2" class="icon-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state border rounded-3">
                    <p class="mb-2">{{ __('No posters yet. When there are none, no pop-up appears on the website.') }}</p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#create-poster">{{ __('Add poster') }}</button>
                </div>
            </div>
        @endforelse
    </div>

    @foreach ([null, ...$posters] as $item)
        @php $key = $item ? 'edit-poster-' . $item->id : 'create-poster'; @endphp
        <x-admin.form-modal :id="$key" :files="true"
            :action="$item ? route('home.poster.update', $item->id) : route('home.poster.create')"
            :title="$item ? __('Edit poster') : __('Add poster')" :icon="$item ? 'edit' : 'plus'"
            :submit="$item ? __('Save changes') : __('Add poster')">
            <div class="form-section-title">{{ __('Picture') }}</div>
            <x-admin.media-input :key="$key" name="img" :label="__('Poster picture')" :current="$item?->img"
                accept="image/png,image/jpeg,image/webp" :hint="__('JPG, PNG or WEBP, max 2 MB. Without a picture the poster is not shown.')" />

            <div class="form-section-title">{{ __('Name') }}</div>
            <x-admin.translatable :key="$key" :item="$item" :fields="[
                ['name' => 'name', 'label' => __('Poster name'), 'required' => true, 'max' => 255, 'hint' => __('Used as the picture description (alt text) for accessibility and search engines.')],
            ]" />
        </x-admin.form-modal>

        @if ($item)
            <x-admin.delete-modal :id="'delete-poster-' . $item->id" :action="route('home.poster.delete', $item->id)" :item-name="$item->name_id"
                :message="__('Delete this poster? It will no longer pop up on the website.')" />
        @endif
    @endforeach
@endsection
