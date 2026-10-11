{{--
    Daftar butir "nama + deskripsi" (layanan, keunggulan, produk). Jumlah butir tetap, hanya bisa diubah.
    Variabel: $items, $title, $icon, $description, $location, $previewUrl, $guide (array teks),
              $updateRoute, $headingRoute, $prefix, $nameField (name|title), $nameLabel, $descLabel, $descMax (opsional)
--}}
<x-admin.page-header :title="$title" :icon="$icon" :description="$description" :location="$location" :preview-url="$previewUrl">
    <x-slot:guide>
        @foreach ($guide as $line)
            <li>{{ $line }}</li>
        @endforeach
        <li>{!! __('The heading above this list is changed in :link.', ['link' => '<a href="' . e(route($headingRoute)) . '">' . e(__('Section Headings')) . '</a>']) !!}</li>
    </x-slot:guide>
</x-admin.page-header>

<div class="table-responsive">
    <table class="table table-admin table-hover align-middle border">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 26%;">{{ $nameLabel }}</th>
                <th>{{ $descLabel }}</th>
                <th style="width: 13%;">{{ __('Last Updated') }}</th>
                <th class="text-end" style="width: 10%;">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="text-muted">{{ $loop->iteration }}</td>
                    <td class="fw-semibold"><x-admin.bilingual :id="$item->{$nameField . '_id'}" :en="$item->{$nameField . '_en'}" :limit="80" /></td>
                    <td class="small"><x-admin.bilingual :id="strip_tags((string) $item->desc_id)" :en="strip_tags((string) $item->desc_en)" :limit="140" /></td>
                    <td class="small"><x-admin.updated-at :value="$item->updated_at" /></td>
                    <td class="text-end">
                        <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#{{ $prefix }}-{{ $item->id }}">
                            <i data-feather="edit-2" class="icon-xs"></i> {{ __('Edit') }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-state">{{ __('No data yet.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@foreach ($items as $item)
    <x-admin.form-modal :id="$prefix . '-' . $item->id" :action="route($updateRoute, $item->id)"
        :title="__('Edit') . ': ' . \Illuminate\Support\Str::limit((string) $item->{$nameField . '_id'}, 50)">
        <x-admin.translatable :key="$prefix . '-' . $item->id" :item="$item" :fields="[
            ['name' => $nameField, 'label' => $nameLabel, 'required' => true, 'max' => 255, 'hint' => __('Shown as the bold heading of this item.')],
            ['name' => 'desc', 'label' => $descLabel, 'type' => 'textarea', 'rows' => 5, 'required' => true, 'max' => $descMax ?? null, 'hint' => __('A short explanation, ideally 1–3 sentences.')],
        ]" />
    </x-admin.form-modal>
@endforeach
