{{--
    Form artikel (create & edit).
    Variabel: $article (null saat create), $categories, $action, $method
--}}
@php
    use App\Models\Blog\Article;

    $isEdit = isset($article);
    $choice = old('publication', $isEdit ? $article->publication_choice : \App\Models\Blog\Article::CHOICE_PRIVATE);
    $scheduleValue = old('publish_date', $isEdit && $article->publication_status === \App\Models\Blog\Article::PUBLICATION_SCHEDULED
        ? $article->publish_date->format('Y-m-d\TH:i')
        : '');
    $alreadyPublished = $isEdit && $article->publication_status === \App\Models\Blog\Article::PUBLICATION_PUBLISHED;
    $hasEnglishError = $errors->has('title_en') || $errors->has('desc_en');
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li class="small">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" id="article-form" novalidate>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="mb-3">
        <label for="category_id" class="form-label fw-semibold">{{ __('Category') }} <span class="text-danger">*</span></label>
        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="" hidden>-- {{ __('Choose') }} --</option>
            @foreach ($categories as $item)
                <option value="{{ $item->id }}" @selected(old('category_id', $article->category_id ?? null) == $item->id)>{{ $item->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Konten bilingual --}}
    <div class="card mb-3">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $hasEnglishError && !$errors->has('title_id') && !$errors->has('desc_id') ? '' : 'active' }}" data-bs-toggle="tab" data-bs-target="#content-id" type="button" role="tab">
                        <img src="https://flagcdn.com/w20/id.png" width="18" alt="" class="me-1"> {{ __('Indonesian') }} <span class="text-danger">*</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $hasEnglishError && !$errors->has('title_id') && !$errors->has('desc_id') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#content-en" type="button" role="tab">
                        <img src="https://flagcdn.com/w20/us.png" width="18" alt="" class="me-1"> {{ __('English') }} <small class="text-muted">({{ __('optional') }})</small>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body tab-content">
            <div class="tab-pane fade {{ $hasEnglishError && !$errors->has('title_id') && !$errors->has('desc_id') ? '' : 'show active' }}" id="content-id" role="tabpanel">
                <div class="mb-3">
                    <label for="title_id" class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
                    <input type="text" name="title_id" id="title_id" class="form-control @error('title_id') is-invalid @enderror" value="{{ old('title_id', $article->title_id ?? '') }}">
                    <div class="form-text">{{ __('The article URL is generated from the Indonesian title. If the title changes, the old URL keeps working and redirects to the new one.') }}</div>
                </div>
                <label for="editor-id" class="form-label fw-semibold">{{ __('Content') }} <span class="text-danger">*</span></label>
                <textarea name="desc_id" id="editor-id" class="form-control" rows="10">{{ old('desc_id', $article->desc_id ?? '') }}</textarea>
            </div>
            <div class="tab-pane fade {{ $hasEnglishError && !$errors->has('title_id') && !$errors->has('desc_id') ? 'show active' : '' }}" id="content-en" role="tabpanel">
                <div class="alert alert-info small py-2">
                    <i data-feather="info" class="icon-sm me-1"></i>
                    {{ __('If the English version is left empty, visitors who choose English will see the Indonesian version.') }}
                </div>
                <div class="mb-3">
                    <label for="title_en" class="form-label fw-semibold">{{ __('Title') }}</label>
                    <input type="text" name="title_en" id="title_en" class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en', $article->title_en ?? '') }}">
                </div>
                <label for="editor-en" class="form-label fw-semibold">{{ __('Content') }}</label>
                <textarea name="desc_en" id="editor-en" class="form-control" rows="10">{{ old('desc_en', $article->desc_en ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- SEO: opsional, bila kosong otomatis memakai judul & cuplikan isi --}}
    <div class="card mb-3">
        <div class="card-header bg-white d-flex align-items-center gap-2">
            <i data-feather="search" class="icon-sm"></i>
            <span class="fw-semibold">{{ __('SEO (search engines & AI)') }}</span>
            <small class="text-muted">({{ __('optional') }})</small>
        </div>
        <div class="card-body">
            <p class="small text-muted mb-3">{{ __('Controls how this article appears on Google and in AI answers. Leave empty to use the article title and the beginning of its content automatically.') }}</p>
            <div class="row g-4">
                @foreach (['id' => __('Indonesian'), 'en' => __('English')] as $lang => $langLabel)
                    <div class="col-lg-6 seo-group" data-lang="{{ $lang }}">
                        <p class="fw-semibold small mb-2">{{ $langLabel }}</p>
                        <div class="mb-2">
                            <label for="meta_title_{{ $lang }}" class="form-label small mb-1">{{ __('SEO title') }}</label>
                            <input type="text" name="meta_title_{{ $lang }}" id="meta_title_{{ $lang }}" maxlength="70"
                                class="form-control form-control-sm seo-title @error('meta_title_' . $lang) is-invalid @enderror"
                                value="{{ old('meta_title_' . $lang, $article->{'meta_title_' . $lang} ?? '') }}">
                            <div class="form-text"><span class="seo-count" data-for="meta_title_{{ $lang }}" data-ideal="60">0</span>/60 · {{ __('ideal 50–60 characters') }}</div>
                        </div>
                        <div class="mb-2">
                            <label for="meta_description_{{ $lang }}" class="form-label small mb-1">{{ __('Meta description') }}</label>
                            <textarea name="meta_description_{{ $lang }}" id="meta_description_{{ $lang }}" rows="2" maxlength="320"
                                class="form-control form-control-sm seo-description @error('meta_description_' . $lang) is-invalid @enderror">{{ old('meta_description_' . $lang, $article->{'meta_description_' . $lang} ?? '') }}</textarea>
                            <div class="form-text"><span class="seo-count" data-for="meta_description_{{ $lang }}" data-ideal="160">0</span>/160 · {{ __('ideal 120–160 characters, summarise the answer the article gives') }}</div>
                        </div>
                        <div class="border rounded p-2 bg-light">
                            <div class="small text-muted mb-1">{{ __('Google preview') }}</div>
                            <div class="seo-preview-url small text-success text-truncate">{{ url('/blog') }}/…</div>
                            <div class="seo-preview-title text-primary fw-semibold text-truncate"></div>
                            <div class="seo-preview-description small text-muted"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mb-4">
        <label for="img" class="form-label fw-semibold">{{ __('Image') }} @unless ($isEdit)<span class="text-danger">*</span>@endunless <small class="text-muted">(JPG, PNG, WEBP · {{ __('max 2MB') }})</small></label>
        <input class="form-control @error('img') is-invalid @enderror" type="file" id="img" name="img" accept="image/png,image/jpeg,image/webp">
        <div class="mt-2">
            @if ($isEdit)
                <small class="text-muted d-block">{{ __('Current image') }}:</small>
            @endif
            <img src="{{ $isEdit ? asset('storage/' . $article->img) : '' }}" class="img-thumbnail img-preview {{ $isEdit ? '' : 'd-none' }}" alt="{{ __('Preview') }}" style="max-width: 300px;">
        </div>
    </div>

    {{-- Status publikasi --}}
    <fieldset class="mb-3">
        <legend class="form-label fw-semibold fs-6 mb-1">{{ __('Publication status') }} <span class="text-danger">*</span></legend>
        <p class="text-muted small mb-2">{{ __('Choose when this article should appear on the website.') }}</p>

        <div class="row g-3">
            @foreach ([
                \App\Models\Blog\Article::CHOICE_PRIVATE => ['icon' => 'lock', 'color' => 'danger', 'label' => __('Private'), 'desc' => __('Saved as a draft. Not visible to website visitors, regardless of the date.')],
                \App\Models\Blog\Article::CHOICE_PUBLISH => ['icon' => 'globe', 'color' => 'primary', 'label' => __('Publish now'), 'desc' => $alreadyPublished
                    ? __('Already published since :date. Stays visible and keeps its publish date.', ['date' => $article->publish_date->translatedFormat('d F Y H:i')])
                    : __('Visible on the website immediately after saving.')],
                \App\Models\Blog\Article::CHOICE_SCHEDULE => ['icon' => 'clock', 'color' => 'warning', 'label' => __('Schedule'), 'desc' => __('Hidden until the chosen date & time, then published automatically.')],
            ] as $value => $option)
                <div class="col-md-4">
                    <input type="radio" class="btn-check" name="publication" id="publication-{{ $value }}" value="{{ $value }}" autocomplete="off" @checked($choice === $value)>
                    <label class="publication-option card h-100 border-2 p-3 w-100" for="publication-{{ $value }}" data-color="{{ $option['color'] }}">
                        <span class="d-flex align-items-center gap-2 fw-semibold text-{{ $option['color'] === 'warning' ? 'dark' : $option['color'] }}">
                            <i data-feather="{{ $option['icon'] }}" class="icon-sm"></i> {{ $option['label'] }}
                        </span>
                        <span class="small text-muted mt-1">{{ $option['desc'] }}</span>
                    </label>
                </div>
            @endforeach
        </div>
    </fieldset>

    <div id="schedule-field" class="mb-3 {{ $choice === \App\Models\Blog\Article::CHOICE_SCHEDULE ? '' : 'd-none' }}">
        <label for="publish_date" class="form-label fw-semibold">{{ __('Publish on') }} <span class="text-danger">*</span></label>
        <input type="datetime-local" name="publish_date" id="publish_date" class="form-control @error('publish_date') is-invalid @enderror" style="max-width: 280px;" value="{{ $scheduleValue }}">
        <div class="form-text">{{ __('Time zone: :timezone. The article appears automatically at this time, no further action is needed.', ['timezone' => config('app.timezone')]) }}</div>
    </div>

    <div id="publication-summary" class="alert mb-4" role="status"></div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('blog.articles.index') }}" class="btn btn-danger btn-sm d-flex align-items-center px-3 py-2 rounded-3">
            <i data-feather="chevron-left" class="icon-sm"></i> {{ __('Back') }}
        </a>
        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center px-3 py-2 rounded-3">
            <i data-feather="save" class="icon-sm me-1"></i> {{ $isEdit ? __('Update') : __('Save') }}
        </button>
    </div>
</form>

@push('css')
    <style>
        .publication-option { cursor: pointer; transition: border-color .15s, box-shadow .15s; }
        .btn-check:checked + .publication-option[data-color="danger"] { border-color: var(--bs-danger); box-shadow: 0 0 0 .2rem rgba(220, 53, 69, .15); }
        .btn-check:checked + .publication-option[data-color="primary"] { border-color: var(--bs-primary); box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15); }
        .btn-check:checked + .publication-option[data-color="warning"] { border-color: var(--bs-warning); box-shadow: 0 0 0 .2rem rgba(255, 193, 7, .25); }
        .btn-check:focus-visible + .publication-option { outline: 2px solid var(--bs-primary); outline-offset: 2px; }
    </style>
@endpush

@php
    $formI18n = [
        'locale' => str_replace('_', '-', app()->getLocale()),
        'alreadyPublished' => $alreadyPublished,
        'private' => [
            'summary' => __('This article will be saved as Private and will not appear on the website.'),
            'title' => __('Save as Private?'),
            'text' => __('The article will not be visible to visitors until you publish or schedule it.'),
            'confirm' => __('Yes, save as Private'),
        ],
        'publish' => [
            'summary' => $alreadyPublished
                ? __('This article is already published and will stay visible on the website.')
                : __('This article will be visible on the website immediately after saving.'),
            'title' => __('Publish this article now?'),
            'text' => $alreadyPublished
                ? __('The changes will be visible to visitors immediately.')
                : __('The article will be visible to all visitors immediately.'),
            'confirm' => __('Yes, publish now'),
        ],
        'schedule' => [
            'summary' => __('This article will stay hidden and be published automatically on :date.'),
            'title' => __('Schedule this article?'),
            'text' => __('The article will stay hidden and appear automatically on :date.'),
            'confirm' => __('Yes, schedule it'),
            'missing' => __('Choose the date and time when the article should be published.'),
            'past' => __('The schedule must be in the future. Choose "Publish now" to publish immediately.'),
        ],
        'cancel' => __('Check again'),
        'missingTitle' => __('Indonesian title and content are required.'),
    ];
@endphp

@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>

    <script>
        (function () {
            const i18n = @json($formI18n);

            const form = document.getElementById('article-form');
            const scheduleField = document.getElementById('schedule-field');
            const scheduleInput = document.getElementById('publish_date');
            const summary = document.getElementById('publication-summary');
            const editors = {};
            let confirmed = false;

            ['id', 'en'].forEach(function (lang) {
                ClassicEditor
                    .create(document.querySelector('#editor-' + lang), {
                        ckfinder: {
                            uploadUrl: '/laravel-filemanager/upload?type=Images&_token={{ csrf_token() }}'
                        }
                    })
                    .then(editor => { editors[lang] = editor; })
                    .catch(error => console.error(error));
            });

            const selected = () => form.querySelector('input[name="publication"]:checked')?.value;

            const formatDate = (value) => new Date(value).toLocaleString(i18n.locale, {
                day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });

            function render() {
                const choice = selected();
                scheduleField.classList.toggle('d-none', choice !== 'schedule');

                const classes = { private: 'alert-danger', publish: 'alert-primary', schedule: 'alert-warning' };
                summary.className = 'alert mb-4 ' + (classes[choice] || 'd-none');

                if (choice === 'schedule') {
                    summary.textContent = scheduleInput.value
                        ? i18n.schedule.summary.replace(':date', formatDate(scheduleInput.value))
                        : i18n.schedule.missing;
                } else if (choice) {
                    summary.textContent = i18n[choice].summary;
                }
            }

            form.querySelectorAll('input[name="publication"]').forEach(el => el.addEventListener('change', render));
            scheduleInput.addEventListener('input', render);
            render();

            form.addEventListener('submit', function (event) {
                if (confirmed) {
                    return; // lanjutkan submit asli (CKEditor menyalin isi ke textarea)
                }

                event.preventDefault();

                const choice = selected();
                const titleId = document.getElementById('title_id').value.trim();
                const descId = editors.id ? editors.id.getData().trim() : document.getElementById('editor-id').value.trim();

                if (!titleId || !descId) {
                    Swal.fire({ icon: 'warning', text: i18n.missingTitle });
                    return;
                }

                if (choice === 'schedule') {
                    if (!scheduleInput.value) {
                        Swal.fire({ icon: 'warning', text: i18n.schedule.missing });
                        return;
                    }
                    if (new Date(scheduleInput.value) <= new Date()) {
                        Swal.fire({ icon: 'warning', text: i18n.schedule.past });
                        return;
                    }
                }

                const copy = i18n[choice];
                const icons = { private: 'info', publish: 'question', schedule: 'question' };

                Swal.fire({
                    title: copy.title,
                    text: copy.text.replace(':date', choice === 'schedule' ? formatDate(scheduleInput.value) : ''),
                    icon: icons[choice],
                    showCancelButton: true,
                    confirmButtonText: copy.confirm,
                    cancelButtonText: i18n.cancel,
                    reverseButtons: true,
                }).then(result => {
                    if (result.isConfirmed) {
                        confirmed = true;
                        form.requestSubmit();
                    }
                });
            });

            // SEO: penghitung karakter & pratinjau hasil pencarian
            const siteName = @json(config('seo.site_name'));
            const plain = html => {
                const div = document.createElement('div');
                div.innerHTML = html || '';
                return (div.textContent || '').replace(/\s+/g, ' ').trim();
            };
            const truncate = (text, max) => text.length > max ? text.slice(0, max - 1).trimEnd() + '…' : text;

            function renderSeo() {
                form.querySelectorAll('.seo-count').forEach(counter => {
                    const length = document.getElementById(counter.dataset.for).value.length;
                    counter.textContent = length;
                    counter.classList.toggle('text-danger', length > Number(counter.dataset.ideal));
                });

                form.querySelectorAll('.seo-group').forEach(group => {
                    const lang = group.dataset.lang;
                    const title = group.querySelector('.seo-title').value.trim()
                        || document.getElementById('title_' + lang).value.trim()
                        || document.getElementById('title_id').value.trim();
                    const content = editors[lang] ? editors[lang].getData() : document.getElementById('editor-' + lang).value;
                    const fallbackContent = editors.id ? editors.id.getData() : document.getElementById('editor-id').value;
                    const description = group.querySelector('.seo-description').value.trim()
                        || plain(content) || plain(fallbackContent);

                    group.querySelector('.seo-preview-title').textContent = title ? truncate(title, 60) + ' | ' + siteName : '—';
                    group.querySelector('.seo-preview-description').textContent = truncate(description, 160) || '—';
                });
            }

            form.addEventListener('input', renderSeo);
            const waitForEditors = setInterval(() => {
                if (editors.id && editors.en) {
                    clearInterval(waitForEditors);
                    Object.values(editors).forEach(editor => editor.model.document.on('change:data', renderSeo));
                    renderSeo();
                }
            }, 300);
            renderSeo();

            // Preview gambar
            document.getElementById('img').addEventListener('change', function (e) {
                const preview = document.querySelector('.img-preview');
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = ev => {
                    preview.src = ev.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            });
        })();
    </script>
@endpush
