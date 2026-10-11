{{--
    Loading screen RRCONS: lingkaran progres tipis di atas latar putih website.
    Dipasang tepat setelah <body> di semua halaman publik, login, dan panel admin.
    Berdiri sendiri: CSS & JS inline agar tampil sebelum stylesheet lain dimuat,
    tidak bergantung pada Tailwind/Bootstrap. Tanpa JavaScript, layar ini tidak tampil.
--}}
<style>
    html.rr-loading { overflow: hidden; }
    #rr-loader {
        --rr-blue: #2563eb;
        --rr-ink: #0f172a;
        --rr-muted: #64748b;
        --rr-faint: #94a3b8;
        --rr-track: #dbeafe;
        --rr-size: 120px;
        position: fixed; inset: 0; z-index: 2147483000;
        background: #fff; color: var(--rr-ink);
        font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', sans-serif;
        opacity: 1; visibility: visible;
        transition: opacity .2s ease, visibility 0s linear 0s;
    }
    #rr-loader.rr-loader--off { opacity: 0; visibility: hidden; pointer-events: none; transition: opacity .2s ease, visibility 0s linear .2s; }
    #rr-loader[hidden] { display: none; }

    #rr-loader .rr-loader__ring {
        position: absolute; left: 50%; top: 50%; width: var(--rr-size); height: var(--rr-size);
        margin: calc(var(--rr-size) / -2) 0 0 calc(var(--rr-size) / -2); padding: 0;
        border: 0; border-radius: 50%; background: transparent; color: inherit; font: inherit;
        cursor: pointer; -webkit-tap-highlight-color: transparent;
        transition: opacity .2s ease;
    }
    #rr-loader .rr-loader__ring::before {
        content: ''; position: absolute; inset: 6px; border-radius: 50%;
        background: var(--rr-blue); opacity: 0; transform: scale(.9);
        transition: opacity .25s ease, transform .35s cubic-bezier(.2, .8, .2, 1);
    }
    #rr-loader .rr-loader__ring:focus { outline: none; }
    #rr-loader .rr-loader__ring:focus-visible { outline: 2px solid var(--rr-blue); outline-offset: 6px; }
    #rr-loader svg { position: absolute; inset: 0; width: 100%; height: 100%; transform: rotate(-90deg); }
    #rr-loader .rr-loader__track { stroke: var(--rr-track); }
    #rr-loader .rr-loader__arc { stroke: var(--rr-blue); }

    #rr-loader .rr-loader__value,
    #rr-loader .rr-loader__skip {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        transition: opacity .2s ease, transform .3s cubic-bezier(.2, .8, .2, 1);
    }
    #rr-loader .rr-loader__value { font-size: 28px; font-weight: 500; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    #rr-loader .rr-loader__value sup { font-size: 11px; font-weight: 500; color: var(--rr-faint); margin: -12px 0 0 2px; letter-spacing: 0; }
    #rr-loader .rr-loader__skip {
        font-size: 11px; font-weight: 500; letter-spacing: .16em; text-transform: uppercase; color: #fff;
        opacity: 0; transform: translateY(6px);
    }

    @media (hover: hover) {
        #rr-loader .rr-loader__ring:hover::before { opacity: 1; transform: scale(1); }
        #rr-loader .rr-loader__ring:hover .rr-loader__value { opacity: 0; transform: translateY(-6px); }
        #rr-loader .rr-loader__ring:hover .rr-loader__skip { opacity: 1; transform: none; }
    }
    #rr-loader .rr-loader__ring:focus-visible::before { opacity: 1; transform: scale(1); }
    #rr-loader .rr-loader__ring:focus-visible .rr-loader__value { opacity: 0; }
    #rr-loader .rr-loader__ring:focus-visible .rr-loader__skip { opacity: 1; transform: none; }

    #rr-loader .rr-loader__status,
    #rr-loader .rr-loader__hint {
        position: absolute; left: 16px; right: 16px; margin: 0; text-align: center;
        transition: opacity .3s ease;
    }
    #rr-loader .rr-loader__status { top: calc(50% + var(--rr-size) / 2 + 28px); font-size: 13px; color: var(--rr-muted); }
    #rr-loader .rr-loader__hint { top: calc(50% + var(--rr-size) / 2 + 52px); font-size: 12px; color: var(--rr-faint); opacity: 0; }
    #rr-loader.rr-loader--hint .rr-loader__hint { opacity: 1; }

    /* Selesai: lingkaran membuka dan halaman tampil dari tengah */
    #rr-loader.rr-loader--reveal { pointer-events: none; }
    #rr-loader.rr-loader--reveal .rr-loader__ring,
    #rr-loader.rr-loader--reveal .rr-loader__status,
    #rr-loader.rr-loader--reveal .rr-loader__hint { opacity: 0; }

    @media (prefers-reduced-motion: reduce) {
        #rr-loader *, #rr-loader *::before { transition-duration: .01s !important; }
    }
</style>
<noscript><style>#rr-loader { display: none !important; } html.rr-loading { overflow: auto; }</style></noscript>

<div id="rr-loader">
    <button type="button" class="rr-loader__ring" data-rr-loader-skip aria-label="{{ __('loader.skip') }}">
        <svg viewBox="0 0 120 120" aria-hidden="true" focusable="false">
            <circle class="rr-loader__track" cx="60" cy="60" r="58" fill="none" stroke-width="2" />
            <circle class="rr-loader__arc" data-rr-loader-arc cx="60" cy="60" r="58" fill="none"
                stroke-width="2" stroke-linecap="round"
                stroke-dasharray="364.42" stroke-dashoffset="364.42" />
        </svg>
        <span class="rr-loader__value" aria-hidden="true"><span data-rr-loader-value>0</span><sup>%</sup></span>
        <span class="rr-loader__skip" aria-hidden="true">{{ __('loader.skip') }}</span>
    </button>
    <p class="rr-loader__status" data-rr-loader-status role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"
        aria-label="{{ __('loader.label') }}">{{ __('loader.status.start') }}</p>
    <p class="rr-loader__hint" aria-hidden="true">{{ __('loader.hint') }}</p>
</div>

<script>
    (function () {
        var root = document.documentElement;
        var el = document.getElementById('rr-loader');
        if (!el) return;

        var text = @json(__('loader.status'));
        var arc = el.querySelector('[data-rr-loader-arc]');
        var value = el.querySelector('[data-rr-loader-value]');
        var status = el.querySelector('[data-rr-loader-status]');
        var CIRCUMFERENCE = 364.42; // 2 * PI * r(58)
        var RADIUS = 60;            // setengah ukuran lingkaran (px), titik awal animasi membuka
        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        var shown = 0, target = 0, cap = 30, active = true, finishing = false, frame = null, revealFrame = null;
        var timers = [];

        root.classList.add('rr-loading');

        function later(fn, ms) { timers.push(setTimeout(fn, ms)); }
        function clearTimers() { timers.forEach(clearTimeout); timers = []; }
        function setStatus(message) { if (status.textContent !== message) status.textContent = message; }
        function setMask(value) { el.style.webkitMaskImage = value; el.style.maskImage = value; }

        function paint() {
            var pct = Math.round(shown);
            arc.style.strokeDashoffset = (CIRCUMFERENCE * (1 - shown / 100)).toFixed(2);
            value.textContent = pct;
            status.setAttribute('aria-valuenow', pct);
        }

        function tick() {
            // Merayap pelan ke batas fase saat ini agar lingkaran tidak pernah terlihat macet
            if (!finishing && target < cap) target += (cap - target) * 0.012;
            var step = (target - shown) * (reduceMotion ? 1 : (finishing ? 0.18 : 0.12));
            shown = Math.min(100, shown + (step > 0 ? Math.max(step, 0.25) : 0));
            if (shown > target) shown = target;
            paint();

            if (finishing && shown >= 99.5) {
                shown = 100; paint();
                setStatus(text.ready);
                frame = null;
                later(hide, reduceMotion ? 0 : 160);
                return;
            }
            frame = active ? requestAnimationFrame(tick) : null;
        }

        function start() {
            if (!frame) frame = requestAnimationFrame(tick);
        }

        function show(mode) {
            clearTimers();
            if (revealFrame) { cancelAnimationFrame(revealFrame); revealFrame = null; }
            active = true; finishing = false;
            shown = target = 0; cap = 90;
            paint();
            setStatus(text[mode] || text.start);
            setMask('');
            el.hidden = false;
            el.classList.remove('rr-loader--off', 'rr-loader--hint', 'rr-loader--reveal');
            root.classList.add('rr-loading');
            later(function () { el.classList.add('rr-loader--hint'); }, 3000);
            start();
        }

        function done() {
            revealFrame = null;
            el.classList.add('rr-loader--off');
            later(function () {
                el.hidden = true;
                setMask('');
                el.classList.remove('rr-loader--reveal');
            }, 220);
        }

        // Lubang melingkar membesar dari lingkaran progres sampai menutupi layar
        function reveal() {
            if (reduceMotion || document.hidden) { done(); return; }
            el.classList.add('rr-loader--reveal');
            var from = RADIUS, to = Math.hypot(window.innerWidth, window.innerHeight) / 2 + 2, begin = null;
            var step = function (now) {
                if (begin === null) begin = now;
                var p = Math.min(1, (now - begin) / 700);
                var eased = p < .5 ? 4 * p * p * p : 1 - Math.pow(-2 * p + 2, 3) / 2;
                var r = from + (to - from) * eased;
                setMask('radial-gradient(circle at 50% 50%, transparent ' + r.toFixed(1) + 'px, #000 ' + (r + 1).toFixed(1) + 'px)');
                if (p < 1) revealFrame = requestAnimationFrame(step); else done();
            };
            revealFrame = requestAnimationFrame(step);
        }

        function hide() {
            if (!active) return;
            clearTimers();
            active = false; finishing = false;
            if (frame) { cancelAnimationFrame(frame); frame = null; }
            root.classList.remove('rr-loading');
            reveal();
            if (window.AOS && typeof window.AOS.refresh === 'function') window.AOS.refresh();
            document.dispatchEvent(new CustomEvent('rr:loaded'));
        }

        function finish() {
            if (!active || finishing) return;
            if (document.hidden) { hide(); return; } // tab di belakang: animasi tidak berjalan
            finishing = true;
            target = 100;
            setStatus(text.finishing);
            start();
        }

        // Progres nyata: struktur halaman (DOM) lalu gambar yang langsung tampil
        function trackImages() {
            if (!active || finishing) return;
            var images = Array.prototype.filter.call(document.images, function (img) {
                return !el.contains(img) && img.getAttribute('loading') !== 'lazy'
                    && (img.getAttribute('src') || img.getAttribute('srcset'));
            });
            var total = images.length, loaded = 0;

            function update() {
                if (!active || finishing) return;
                if (total === 0) { cap = 95; target = Math.max(target, 90); setStatus(text.finishing); return; }
                target = Math.max(target, 30 + 60 * (loaded / total));
                cap = Math.max(cap, Math.min(95, 30 + 60 * ((loaded + 1) / total)));
                setStatus(text.media.replace(':loaded', loaded).replace(':total', total));
            }

            images.forEach(function (img) {
                if (img.complete) { loaded++; return; }
                var once = function () { loaded++; update(); };
                img.addEventListener('load', once, { once: true });
                img.addEventListener('error', once, { once: true });
            });
            update();
        }

        setStatus(text.content);
        later(function () { el.classList.add('rr-loader--hint'); }, 3000);
        later(finish, 12000); // batas aman bila ada resource yang tidak pernah selesai
        start();

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { cap = 40; target = Math.max(target, 30); trackImages(); });
        } else {
            trackImages();
        }
        if (document.readyState === 'complete') finish();
        else window.addEventListener('load', finish);

        // Interaksi: lingkaran adalah tombol "Lewati" (juga tombol Esc)
        el.querySelector('[data-rr-loader-skip]').addEventListener('click', hide);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && active) hide();
        });

        // Saat pindah halaman di dalam situs, tampilkan lagi (hanya bila tidak instan)
        function sameSiteNavigation(link, e) {
            if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return false;
            if (link.hasAttribute('download') || link.hasAttribute('data-no-loader')) return false;
            if (link.target && link.target !== '_self') return false;
            var url;
            try { url = new URL(link.href, location.href); } catch (err) { return false; }
            if (url.origin !== location.origin || !/^https?:$/.test(url.protocol)) return false;
            if (url.pathname === location.pathname && url.search === location.search) return false; // hanya pindah #anchor
            return true;
        }

        var pending = null;
        function showSoon(isStillValid, safetyMs) {
            clearTimeout(pending);
            pending = setTimeout(function () {
                if (!isStillValid()) return;
                show('navigating');
                later(hide, safetyMs); // mis. file diunduh dan halaman tidak berganti
            }, 150);
        }

        document.addEventListener('click', function (e) {
            var link = e.target.closest && e.target.closest('a[href]');
            if (!link || el.contains(link) || !sameSiteNavigation(link, e)) return;
            showSoon(function () { return !e.defaultPrevented; }, 10000);
        });

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (form.hasAttribute('data-no-loader') || (form.target && form.target !== '_self')) return;
            if ((form.getAttribute('method') || '').toLowerCase() === 'dialog') return;
            // Dicek setelah jeda: handler lain (AJAX) mungkin memanggil preventDefault belakangan
            showSoon(function () { return !e.defaultPrevented; }, 45000);
        });

        // Kembali lewat tombol Back (bfcache): jangan biarkan layar loading tertinggal
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) { clearTimeout(pending); hide(); }
        });
    })();
</script>
