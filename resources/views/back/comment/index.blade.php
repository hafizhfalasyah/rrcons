@extends('back.layout.template')

@section('title', 'RRCONS Admin | ' . __('Comment Moderation'))

@section('content')
@php
    $tabs = [
        \App\Models\Blog\Comment::STATUS_PENDING => __('Pending'),
        \App\Models\Blog\Comment::STATUS_APPROVED => __('Approved'),
        \App\Models\Blog\Comment::STATUS_SPAM => __('Spam'),
        \App\Http\Controllers\Back\Blog\CommentController::FILTER_ALL => __('All'),
    ];
    $badges = [
        \App\Models\Blog\Comment::STATUS_PENDING => 'bg-warning text-dark',
        \App\Models\Blog\Comment::STATUS_APPROVED => 'bg-success',
        \App\Models\Blog\Comment::STATUS_SPAM => 'bg-secondary',
    ];
    $actions = [
        \App\Models\Blog\Comment::STATUS_APPROVED => ['label' => __('Approve'), 'icon' => 'check', 'class' => 'btn-success'],
        \App\Models\Blog\Comment::STATUS_PENDING => ['label' => __('Move to pending'), 'icon' => 'clock', 'class' => 'btn-outline-secondary'],
        \App\Models\Blog\Comment::STATUS_SPAM => ['label' => __('Mark as spam'), 'icon' => 'slash', 'class' => 'btn-outline-warning'],
    ];
@endphp

    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h3 fw-bold text-uppercase">{{ __('Comment Moderation') }}</h1>
    </div>

    <p class="text-muted small">{{ __('New comments only appear on the website after they are approved.') }}</p>

    {{-- Flash & Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <ul class="nav nav-pills">
            @foreach ($tabs as $key => $label)
                <li class="nav-item">
                    <a class="nav-link {{ $status === $key ? 'active' : '' }}"
                        href="{{ route('blog.comments.index', array_filter(['status' => $key, 'q' => $search])) }}">
                        {{ $label }}
                        <span class="badge {{ $status === $key ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">
                            {{ $key === \App\Http\Controllers\Back\Blog\CommentController::FILTER_ALL ? $total : ($counts[$key] ?? 0) }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>

        <form action="{{ route('blog.comments.index') }}" method="get" class="d-flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="{{ __('Search name, email or comment') }}">
            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                <i data-feather="search" class="icon-sm"></i>
            </button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle shadow-sm">
            <thead class="table-primary text-center">
                <tr>
                    <th style="width: 5%;">{{ __('No') }}</th>
                    <th style="width: 17%;">{{ __('Commenter') }}</th>
                    <th>{{ __('Comment') }}</th>
                    <th style="width: 18%;">{{ __('Article') }}</th>
                    <th style="width: 10%;">{{ __('Status') }}</th>
                    <th style="width: 16%;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($comments as $comment)
                    <tr>
                        <td class="text-center">{{ $comments->firstItem() + $loop->index }}.</td>
                        <td>
                            <div class="fw-semibold">{{ $comment->name }}</div>
                            <a href="mailto:{{ $comment->email }}" class="small">{{ $comment->email }}</a>
                            <div class="small text-muted">{{ $comment->created_at?->translatedFormat('d F Y H:i') }}</div>
                        </td>
                        <td class="small" style="white-space: pre-line;">{{ $comment->content }}</td>
                        <td class="small">
                            @if ($comment->article)
                                <a href="{{ route('blog.articles.show', $comment->article->id) }}">{{ $comment->article->title_id }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $badges[$comment->status] ?? 'bg-light text-dark' }}">{{ $tabs[$comment->status] ?? $comment->status }}</span>
                            @if ($comment->moderated_at)
                                <div class="small text-muted mt-1">
                                    {{ __('Moderated by :name on :date', [
                                        'name' => $comment->moderator?->name ?? '-',
                                        'date' => $comment->moderated_at->translatedFormat('d M Y H:i'),
                                    ]) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @foreach ($actions as $target => $action)
                                    @continue($comment->status === $target)
                                    <form action="{{ route('blog.comments.update', $comment->id) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="{{ $target }}">
                                        <button type="submit" class="btn {{ $action['class'] }} btn-sm w-100 d-flex align-items-center justify-content-center">
                                            <i data-feather="{{ $action['icon'] }}" class="icon-sm me-1"></i> {{ $action['label'] }}
                                        </button>
                                    </form>
                                @endforeach
                                <button type="button" class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center" data-bs-toggle="modal"
                                    data-bs-target="#delete-comment-modal{{ $comment->id }}">
                                    <i data-feather="trash-2" class="icon-sm me-1"></i> {{ __('Delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">{{ __('No comments in this list.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $comments->links('pagination::bootstrap-5') }}
    </div>

    <!-- Delete Comment Modal -->
    @foreach ($comments as $comment)
        <div class="modal fade" id="delete-comment-modal{{ $comment->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delete-comment-modal-label{{ $comment->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-sm border-0">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="delete-comment-modal-label{{ $comment->id }}">{{ __('Confirm Delete') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="fs-6 mb-0">{{ __('Are you sure you want to delete the comment from') }} <strong>{{ $comment->name }}</strong>?</p>
                        <p class="text-muted">{{ __('This action cannot be undone.') }}</p>
                    </div>
                    <div class="modal-footer justify-content-center border-0">
                        <form action="{{ route('blog.comments.destroy', $comment->id) }}" method="post" class="d-inline">
                            @method('DELETE')
                            @csrf
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection
