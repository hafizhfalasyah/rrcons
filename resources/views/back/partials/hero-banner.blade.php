{{--
    Halaman "Banner Utama" (hero): gambar latar besar paling atas sebuah halaman website.
    Variabel: $heroes, $pageName, $previewUrl, $updateRoute, $headingRoute (menu Judul Bagian), $prefix
--}}
<x-admin.page-header :title="__('Main Banner') . ' — ' . $pageName" icon="image"
    :description="__('The large background picture at the very top of the page.')"
    :location="$pageName . ' — ' . __('top of the page')" :preview-url="$previewUrl">
    <x-slot:guide>
        <li>{{ __('Use a landscape photo, at least 1920 × 1080 pixels, in JPG, PNG or WEBP format, max 2 MB.') }}</li>
        <li>{!! __('The text on top of the banner is changed in :link.', ['link' => '<a href="' . e(route($headingRoute)) . '">' . e(__('Section Headings')) . '</a>']) !!}</li>
        <li>{{ __('Large photos are compressed automatically when uploaded, without visible quality loss.') }}</li>
    </x-slot:guide>
</x-admin.page-header>

<div class="row g-3">
    @forelse ($heroes as $item)
        <div class="col-xl-8">
            <div class="panel">
                <div class="panel-body">
                    @if ($item->bg_img)
                        <img src="{{ asset('storage/' . $item->bg_img) }}" alt="{{ __('Main Banner') }}" class="w-100 rounded-3 border" style="max-height: 340px; object-fit: cover;">
                    @else
                        <div class="empty-state border rounded-3">{{ __('No image yet') }}</div>
                    @endif
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                        <div class="small text-muted">{{ __('Last Updated') }}: <x-admin.updated-at :value="$item->updated_at" /></div>
                        <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#{{ $prefix }}-{{ $item->id }}">
                            <i data-feather="upload" class="icon-xs"></i> {{ __('Change banner') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="empty-state border rounded-3">{{ __('No data yet.') }}</div></div>
    @endforelse
</div>

@foreach ($heroes as $item)
    <x-admin.form-modal :id="$prefix . '-' . $item->id" :action="route($updateRoute, $item->id)" :title="__('Change banner') . ' — ' . $pageName"
        icon="image" :files="true" :submit="__('Save banner')">
        <x-admin.media-input :key="$prefix . '-' . $item->id" name="bg_img" :label="__('Banner image')" :current="$item->bg_img"
            accept="image/png,image/jpeg,image/webp,image/gif"
            :hint="__('JPG, PNG, WEBP or GIF, max 2 MB. Recommended: landscape, at least 1920 × 1080 px.')" />
    </x-admin.form-modal>
@endforeach
