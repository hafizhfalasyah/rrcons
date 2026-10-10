<div class="alert alert-light border small mb-3">
    <ul class="mb-1 ps-3">
        <li><strong>Publish</strong> + date/time now or in the past: article is visible immediately.</li>
        <li><strong>Publish</strong> + date/time in the future: article is <strong>scheduled</strong> and appears automatically at that time.</li>
        <li><strong>Private</strong>: article is never visible on the website, regardless of the date.</li>
    </ul>
    <div class="text-muted">Times use the {{ config('app.timezone') }} timezone.</div>
    <div id="schedule-hint" class="fw-semibold mt-2"></div>
</div>

<script>
    (function () {
        const status = document.getElementById('status');
        const publishDate = document.getElementById('publish_date');
        const hint = document.getElementById('schedule-hint');

        function renderHint() {
            const date = publishDate.value ? new Date(publishDate.value) : null;

            if (status.value === '0') {
                hint.className = 'fw-semibold mt-2 text-danger';
                hint.textContent = 'This article will stay private (hidden from the website).';
            } else if (status.value === '1' && date && date > new Date()) {
                hint.className = 'fw-semibold mt-2 text-warning';
                hint.textContent = 'This article will be published automatically on ' + date.toLocaleString() + '.';
            } else if (status.value === '1') {
                hint.className = 'fw-semibold mt-2 text-primary';
                hint.textContent = 'This article will be published immediately.';
            } else {
                hint.textContent = '';
            }
        }

        status.addEventListener('change', renderHint);
        publishDate.addEventListener('input', renderHint);
        renderHint();
    })();
</script>
