<!-- Update User Modal -->
@foreach ($users as $item)
    <div class="modal fade" id="update-user-modal{{ $item->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="update-user-modal-label{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center" id="update-user-modal-label">
                        <i data-feather="edit" class="me-2" style="width: 20px; height: 20px;"></i> {{ __('Edit User') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>

                <!-- Form -->
                <form action="{{ url('users/'.$item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-12">
                                <label for="name-{{ $item->id }}" class="form-label fw-semibold">{{ __('User Name') }}</label>
                                <input type="text" name="name" id="name-{{ $item->id }}" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name) }}" required>

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-12">
                                <label for="email-{{ $item->id }}" class="form-label fw-semibold">{{ __('Email Address') }}</label>
                                <input type="email" name="email" id="email-{{ $item->id }}" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $item->email) }}" required>

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div class="col-12">
                                <label for="role-{{ $item->id }}" class="form-label fw-semibold text-primary">{{ __('Role') }}</label>
                                @php $canChangeRole = auth()->user()->isAdmin() && $item->id != auth()->id(); @endphp
                                <select name="role" id="role-{{ $item->id }}" class="form-select @error('role') is-invalid @enderror" {{ $canChangeRole ? 'required' : 'disabled' }}>
                                    <option value="" disabled selected>{{ __('-- Select Role --') }}</option>
                                    <option value="admin" {{ old('role', $item->role) == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                    <option value="writer" {{ old('role', $item->role) == 'writer' ? 'selected' : '' }}>{{ __('Writer') }}</option>
                                    <option value="uploader" {{ old('role', $item->role) == 'uploader' ? 'selected' : '' }}>{{ __('Uploader') }}</option>
                                </select>

                                @unless ($canChangeRole)
                                    <div class="form-text">{{ __('Only an admin can change another user\'s role.') }}</div>
                                @endunless

                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password-{{ $item->id }}" class="form-label fw-semibold">{{ __('Password') }} <small class="text-muted">{{ __('(Leave blank if not changing)') }}</small></label>
                                <input type="password" name="password" id="password-{{ $item->id }}" class="form-control @error('password') is-invalid @enderror">

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label for="password_confirmation-{{ $item->id }}" class="form-label fw-semibold">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation-{{ $item->id }}" class="form-control @error('password_confirmation') is-invalid @enderror">

                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i data-feather="x" class="me-1"></i> {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> {{ __('Update User') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
