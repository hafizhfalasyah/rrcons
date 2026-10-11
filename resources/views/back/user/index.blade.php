@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . (auth()->user()->isAdmin() ? __('Users') : __('My Account')))

@php
    $isAdmin = auth()->user()->isAdmin();
    $roles = [
        'admin' => ['label' => __('Admin'), 'class' => 'bg-primary', 'desc' => __('Full access: all website content, blog and user accounts.')],
        'writer' => ['label' => __('Writer'), 'class' => 'bg-success', 'desc' => __('Blog only: articles, categories and comments.')],
        'uploader' => ['label' => __('Uploader'), 'class' => 'bg-warning text-dark', 'desc' => __('Only the pop-up posters and the event gallery.')],
    ];
@endphp

@section('content')
    <x-admin.page-header :title="$isAdmin ? __('Users') : __('My Account')" :icon="$isAdmin ? 'users' : 'user'"
        :description="$isAdmin ? __('People who can log in to this admin panel and what they are allowed to do.') : __('Change your name, email address or password.')">
        @if ($isAdmin)
            <x-slot:actions>
                <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#create-user">
                    <i data-feather="user-plus" class="icon-xs"></i> {{ __('Add User') }}
                </button>
            </x-slot:actions>
        @endif
        <x-slot:guide>
            @if ($isAdmin)
                @foreach ($roles as $role)
                    <li><strong>{{ $role['label'] }}</strong> — {{ $role['desc'] }}</li>
                @endforeach
                <li>{{ __('Visitors cannot register themselves; every account is created here by an admin.') }}</li>
            @else
                <li>{{ __('Leave the password fields empty if you do not want to change your password.') }}</li>
                <li>{{ __('Your role can only be changed by an admin.') }}</li>
            @endif
        </x-slot:guide>
    </x-admin.page-header>

    <div class="table-responsive">
        <table class="table table-admin table-hover align-middle border">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email Address') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar border" style="width: 32px; height: 32px;">{{ mb_strtoupper(mb_substr($item->name, 0, 1)) }}</span>
                                <span class="fw-semibold">{{ $item->name }}</span>
                                @if ($item->id === auth()->id())
                                    <span class="badge role-badge">{{ __('You') }}</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $item->email }}</td>
                        <td>
                            @if ($item->role && isset($roles[$item->role]))
                                <span class="badge {{ $roles[$item->role]['class'] }}">{{ $roles[$item->role]['label'] }}</span>
                            @else
                                <span class="badge bg-secondary" title="{{ __('Users without a role cannot open the admin panel.') }}" data-bs-toggle="tooltip">{{ __('No role') }}</span>
                            @endif
                        </td>
                        <td class="small">{{ $item->created_at?->translatedFormat('d F Y') }}</td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#edit-user-{{ $item->id }}">
                                <i data-feather="edit-2" class="icon-xs"></i> {{ __('Edit') }}
                            </button>
                            @if ($isAdmin && $item->id !== auth()->id())
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete-user-{{ $item->id }}" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                    <i data-feather="trash-2" class="icon-xs"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @foreach ($isAdmin ? [null, ...$users] : $users as $item)
        @php
            $key = $item ? 'edit-user-' . $item->id : 'create-user';
            $current = old('_modal') === $key;
            $value = fn ($name) => $current ? old($name) : $item?->{$name};
            $invalid = fn ($name) => $current && $errors->has($name);
            $canChangeRole = $isAdmin && (!$item || $item->id !== auth()->id());
        @endphp
        <x-admin.form-modal :id="$key" :action="$item ? url('users/' . $item->id) : url('users')" :method="$item ? 'PUT' : 'POST'"
            :title="$item ? __('Edit User') . ': ' . $item->name : __('Add User')" :icon="$item ? 'edit' : 'user-plus'"
            :submit="$item ? __('Save changes') : __('Add User')">
            <div class="form-section-title">{{ __('Account') }}</div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="{{ $key }}-name" class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="{{ $key }}-name" class="form-control {{ $invalid('name') ? 'is-invalid' : '' }}" value="{{ $value('name') }}" required minlength="3">
                    @if ($invalid('name'))<div class="invalid-feedback">{{ $errors->first('name') }}</div>@endif
                </div>
                <div class="col-md-6">
                    <label for="{{ $key }}-email" class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="{{ $key }}-email" class="form-control {{ $invalid('email') ? 'is-invalid' : '' }}" value="{{ $value('email') }}" required>
                    @if ($invalid('email'))<div class="invalid-feedback">{{ $errors->first('email') }}</div>@endif
                    <div class="form-text">{{ __('Used to log in.') }}</div>
                </div>
            </div>

            <div class="form-section-title">{{ __('Role') }}</div>
            <div class="mb-3">
                <label for="{{ $key }}-role" class="form-label">{{ __('Role') }} @if ($canChangeRole)<span class="text-danger">*</span>@endif</label>
                <select name="role" id="{{ $key }}-role" class="form-select {{ $invalid('role') ? 'is-invalid' : '' }}" {{ $canChangeRole ? 'required' : 'disabled' }}>
                    <option value="" disabled @selected(!$value('role'))>{{ __('-- Select Role --') }}</option>
                    @foreach ($roles as $role => $meta)
                        <option value="{{ $role }}" @selected($value('role') === $role)>{{ $meta['label'] }} — {{ $meta['desc'] }}</option>
                    @endforeach
                </select>
                @if ($invalid('role'))<div class="invalid-feedback">{{ $errors->first('role') }}</div>@endif
                @unless ($canChangeRole)
                    <div class="form-text">{{ __('Only an admin can change the role of another user.') }}</div>
                @endunless
            </div>

            <div class="form-section-title">{{ __('Password') }}</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="{{ $key }}-password" class="form-label">{{ $item ? __('New password') : __('Password') }} @unless ($item)<span class="text-danger">*</span>@endunless</label>
                    <input type="password" name="password" id="{{ $key }}-password" class="form-control {{ $invalid('password') ? 'is-invalid' : '' }}" autocomplete="new-password" minlength="8" @unless ($item) required @endunless>
                    @if ($invalid('password'))<div class="invalid-feedback">{{ $errors->first('password') }}</div>@endif
                </div>
                <div class="col-md-6">
                    <label for="{{ $key }}-password_confirmation" class="form-label">{{ __('Repeat password') }} @unless ($item)<span class="text-danger">*</span>@endunless</label>
                    <input type="password" name="password_confirmation" id="{{ $key }}-password_confirmation" class="form-control {{ $invalid('password_confirmation') ? 'is-invalid' : '' }}" autocomplete="new-password" minlength="8" @unless ($item) required @endunless>
                    @if ($invalid('password_confirmation'))<div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>@endif
                </div>
                <div class="col-12"><div class="form-text mt-0">{{ $item ? __('Leave both fields empty to keep the current password. Minimum 8 characters.') : __('Minimum 8 characters. Give the password to the user privately.') }}</div></div>
            </div>
        </x-admin.form-modal>

        @if ($item && $isAdmin && $item->id !== auth()->id())
            <x-admin.delete-modal :id="'delete-user-' . $item->id" :action="url('users/' . $item->id)" :item-name="$item->name"
                :message="__('Delete this user? They will no longer be able to log in.')" />
        @endif
    @endforeach
@endsection
