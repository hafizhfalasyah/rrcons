<div class="row g-2 small">
    <div class="col-md-4">
        <div class="border rounded p-2 h-100">
            <span class="badge bg-danger mb-1"><i class="bi bi-lock-fill me-1"></i>{{ __('Private') }}</span>
            <div class="text-muted">{{ __('Draft. Not visible to website visitors, regardless of the date.') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="border rounded p-2 h-100">
            <span class="badge bg-warning text-dark mb-1"><i class="bi bi-clock-fill me-1"></i>{{ __('Scheduled') }}</span>
            <div class="text-muted">{{ __('Hidden until its publish date & time, then published automatically.') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="border rounded p-2 h-100">
            <span class="badge bg-primary mb-1"><i class="bi bi-globe me-1"></i>{{ __('Published') }}</span>
            <div class="text-muted">{{ __('Visible on the website now.') }}</div>
        </div>
    </div>
</div>
