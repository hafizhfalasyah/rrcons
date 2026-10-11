@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Categories'))

@section('content')
    <x-admin.page-header :title="__('Categories')" icon="tag"
        :description="__('Topics used to group blog articles. Visitors can filter the blog by category.')"
        :location="__('Blog page — category filter')" :preview-url="route('blog')">
        <x-slot:actions>
            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#create-category">
                <i data-feather="plus" class="icon-xs"></i> {{ __('Add Category') }}
            </button>
        </x-slot:actions>
        <x-slot:guide>
            <li>{{ __('The web address (slug) of a category is created automatically from its Indonesian name.') }}</li>
            <li>{{ __('A category that still has articles cannot be deleted. Move those articles to another category first.') }}</li>
        </x-slot:guide>
    </x-admin.page-header>

    <div class="table-responsive">
        <table class="table table-admin table-hover align-middle border">
            <thead>
                <tr>
                    <th>{{ __('Category Name') }}</th>
                    <th>{{ __('Slug') }}</th>
                    <th class="text-center">{{ __('Articles') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $item)
                    <tr>
                        <td class="fw-semibold"><x-admin.bilingual :id="$item->name_id" :en="$item->name_en" :limit="60" /></td>
                        <td><code class="small">{{ $item->slug }}</code></td>
                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->articles_count }}</span></td>
                        <td class="small">{{ $item->created_at?->translatedFormat('d F Y') }}</td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#edit-category-{{ $item->id }}">
                                <i data-feather="edit-2" class="icon-xs"></i> {{ __('Edit') }}
                            </button>
                            @if ($item->articles_count > 0)
                                <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="{{ __('Cannot be deleted while it still has articles.') }}">
                                    <button class="btn btn-outline-secondary btn-sm" disabled aria-label="{{ __('Delete') }}"><i data-feather="trash-2" class="icon-xs"></i></button>
                                </span>
                            @else
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete-category-{{ $item->id }}" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                    <i data-feather="trash-2" class="icon-xs"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">{{ __('No data yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @foreach ([null, ...$categories] as $item)
        @php $key = $item ? 'edit-category-' . $item->id : 'create-category'; @endphp
        <x-admin.form-modal :id="$key" :action="$item ? route('blog.categories.update', $item->id) : route('blog.categories.store')"
            :method="$item ? 'PUT' : 'POST'" :title="$item ? __('Edit Category') : __('Add Category')" :icon="$item ? 'edit' : 'plus'"
            :submit="$item ? __('Save changes') : __('Add Category')">
            <x-admin.translatable :key="$key" :item="$item" :fields="[
                ['name' => 'name', 'label' => __('Category Name'), 'required' => true, 'max' => 255, 'hint' => __('At least 3 characters, for example: Business News.')],
            ]" />
        </x-admin.form-modal>

        @if ($item && $item->articles_count === 0)
            <x-admin.delete-modal :id="'delete-category-' . $item->id" :action="route('blog.categories.destroy', $item->id)" :item-name="$item->name_id"
                :message="__('Delete this category?')" />
        @endif
    @endforeach
@endsection
