{{--
    Halaman "Judul Bagian" (tabel *_menus): judul & subjudul setiap bagian di satu halaman website.
    Variabel:
      $menus       : koleksi menu dengan withCount relasi bagian
      $sections    : [relasi => [label bagian, route admin isi bagian]]
      $pageName    : nama halaman website (mis. "Beranda")
      $previewUrl  : URL halaman publik
      $updateRoute : nama route update (menerima id)
      $prefix      : prefix id modal
--}}
@php
    $sectionOf = function ($menu) use ($sections) {
        foreach ($sections as $relation => $section) {
            if (($menu->{\Illuminate\Support\Str::snake($relation) . '_count'} ?? 0) > 0) {
                return $section;
            }
        }

        return null;
    };
@endphp

<x-admin.page-header :title="__('Section Headings') . ' — ' . $pageName" icon="type"
    :description="__('The big title and the short sentence below it that open each section of this page.')"
    :location="$pageName" :preview-url="$previewUrl">
    <x-slot:guide>
        <li>{{ __('Each row is the heading of one section. The "Used for" column tells you which section.') }}</li>
        <li>{{ __('The last word of the title is automatically shown in blue on the website.') }}</li>
        <li>{{ __('The content of the section itself (pictures, list items) is edited in the menu of that section.') }}</li>
    </x-slot:guide>
</x-admin.page-header>

<div class="table-responsive">
    <table class="table table-admin table-hover align-middle border">
        <thead>
            <tr>
                <th style="width: 18%;">{{ __('Used for') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Subtitle') }}</th>
                <th style="width: 13%;">{{ __('Last Updated') }}</th>
                <th class="text-end" style="width: 10%;">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($menus as $item)
                @php $section = $sectionOf($item); @endphp
                <tr>
                    <td>
                        @if ($section)
                            <a href="{{ route($section[1]) }}" class="fw-semibold text-decoration-none">{{ $section[0] }}</a>
                        @else
                            <span class="text-muted">{{ __('Not used yet') }}</span>
                        @endif
                    </td>
                    <td><x-admin.bilingual :id="$item->title_id" :en="$item->title_en" /></td>
                    <td class="small"><x-admin.bilingual :id="$item->sub_title_id" :en="$item->sub_title_en" :limit="110" /></td>
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

@foreach ($menus as $item)
    @php $section = $sectionOf($item); @endphp
    <x-admin.form-modal :id="$prefix . '-' . $item->id" :action="route($updateRoute, $item->id)"
        :title="__('Edit section heading') . ($section ? ' — ' . $section[0] : '')"
        :intro="__('This text appears as the opening heading of the section on the website.')">
        <x-admin.translatable :key="$prefix . '-' . $item->id" :item="$item" :fields="[
            ['name' => 'title', 'label' => __('Title'), 'required' => true, 'max' => 255, 'hint' => __('Short and clear, for example: Our Services. The last word is shown in blue.')],
            ['name' => 'sub_title', 'label' => __('Subtitle'), 'type' => 'textarea', 'rows' => 3, 'max' => 255, 'hint' => __('One or two sentences that explain the section. May be left empty.')],
        ]" />
    </x-admin.form-modal>
@endforeach
