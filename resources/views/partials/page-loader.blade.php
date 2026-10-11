{{--
    Loading screen RRCONS (lingkaran progres + logo).
    Dipasang tepat setelah <body> di semua halaman publik, login, dan panel admin.
    Berdiri sendiri: CSS & JS inline agar tampil sebelum stylesheet lain dimuat,
    tidak bergantung pada Tailwind/Bootstrap. Tanpa JavaScript, layar ini tidak tampil.
--}}
<style>
    html.rr-loading { overflow: hidden; }
    #rr-loader {
        --rr-yellow: #facc15;
        --rr-yellow-deep: #eab308;
        position: fixed; inset: 0; z-index: 2147483000;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 18px;
        background: radial-gradient(circle at 50% 42%, #1d4ed8 0%, #12307f 48%, #081638 100%);
        color: #fff; font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', sans-serif;
        cursor: pointer; user-select: none; -webkit-tap-highlight-color: transparent;
        opacity: 1; visibility: visible;
        transition: opacity .45s ease, visibility 0s linear 0s;
    }
    #rr-loader.rr-loader--off { opacity: 0; visibility: hidden; pointer-events: none; transition: opacity .45s ease, visibility 0s linear .45s; }
    #rr-loader[hidden] { display: none; }
    #rr-loader .rr-loader__stage { perspective: 600px; }
    #rr-loader .rr-loader__ring {
        position: relative; width: 168px; height: 168px; border-radius: 50%;
        transition: transform .25s ease-out, scale .45s ease;
        will-change: transform;
    }
    #rr-loader.rr-loader--off .rr-loader__ring { scale: 1.08; }
    #rr-loader svg { position: absolute; inset: 0; width: 100%; height: 100%; overflow: visible; }
    #rr-loader .rr-loader__orbit { transform-origin: 50% 50%; animation: rr-loader-spin 9s linear infinite; }
    #rr-loader .rr-loader__arc {
        transform: rotate(-90deg); transform-origin: 50% 50%;
        filter: drop-shadow(0 0 4px rgba(250, 204, 21, .55));
    }
    #rr-loader .rr-loader__logo {
        position: absolute; inset: 26px; width: calc(100% - 52px); height: calc(100% - 52px);
        object-fit: contain; opacity: 0; transition: opacity .4s ease;
        animation: rr-loader-breathe 2.4s ease-in-out infinite;
    }
    #rr-loader .rr-loader__logo.is-ready { opacity: 1; }
    #rr-loader .rr-loader__percent {
        font-size: 30px; font-weight: 600; line-height: 1; letter-spacing: .02em;
        font-variant-numeric: tabular-nums;
    }
    #rr-loader .rr-loader__percent small { font-size: 15px; font-weight: 500; color: var(--rr-yellow); margin-left: 2px; }
    #rr-loader .rr-loader__status { margin-top: -8px; min-height: 20px; font-size: 13px; color: rgba(255, 255, 255, .72); text-align: center; padding: 0 16px; }
    #rr-loader .rr-loader__skip {
        position: absolute; bottom: 28px; left: 0; right: 0; text-align: center;
        font-size: 11px; letter-spacing: .18em; text-transform: uppercase; color: rgba(255, 255, 255, .5);
        opacity: 0; transition: opacity .4s ease;
    }
    #rr-loader.rr-loader--hint .rr-loader__skip { opacity: 1; }
    @keyframes rr-loader-spin { to { transform: rotate(360deg); } }
    @keyframes rr-loader-breathe { 0%, 100% { transform: scale(1); } 50% { transform: scale(.96); } }
    @media (prefers-reduced-motion: reduce) {
        #rr-loader .rr-loader__orbit, #rr-loader .rr-loader__logo { animation: none; }
        #rr-loader, #rr-loader .rr-loader__ring { transition-duration: .01s; }
    }
</style>
<noscript><style>#rr-loader { display: none !important; } html.rr-loading { overflow: auto; }</style></noscript>

<div id="rr-loader" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"
    aria-label="{{ __('loader.label') }}">
    <div class="rr-loader__stage">
        <div class="rr-loader__ring" data-rr-loader-ring>
            <svg viewBox="0 0 100 100" aria-hidden="true" focusable="false">
                <defs>
                    <linearGradient id="rr-loader-gradient" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#fde047" />
                        <stop offset="1" stop-color="#eab308" />
                    </linearGradient>
                </defs>
                <circle class="rr-loader__orbit" cx="50" cy="50" r="49" fill="none" stroke="rgba(255,255,255,.22)" stroke-width=".6" stroke-dasharray="1.2 4.2" />
                <circle cx="50" cy="50" r="44" fill="none" stroke="rgba(255,255,255,.12)" stroke-width="3.2" />
                <circle class="rr-loader__arc" data-rr-loader-arc cx="50" cy="50" r="44" fill="none"
                    stroke="url(#rr-loader-gradient)" stroke-width="3.2" stroke-linecap="round"
                    stroke-dasharray="276.46" stroke-dashoffset="276.46" />
            </svg>
            <img class="rr-loader__logo" src="{{ asset('front/image/logo.png') }}" alt="" width="116" height="116" decoding="async"
                onload="this.classList.add('is-ready')">
        </div>
    </div>
    <div class="rr-loader__percent" aria-hidden="true"><span data-rr-loader-value>0</span><small>%</small></div>
    <div class="rr-loader__status" data-rr-loader-status>{{ __('loader.status.start') }}</div>
    <div class="rr-loader__skip" aria-hidden="true">{{ __('loader.skip') }}</div>
</div>

<script>
    (function () {
        var root = document.documentElement;
        var el = document.getElementById('rr-loader');
        if (!el) return;

        var text = @json(__('loader.status'));
        var arc = el.querySelector('[data-rr-loader-arc]');
        var ring = el.querySelector('[data-rr-loader-ring]');
        var value = el.querySelector('[data-rr-loader-value]');
        var status = el.querySelector('[data-rr-loader-status]');
        var logo = el.querySelector('.rr-loader__logo');
        var CIRCUMFERENCE = 276.46; // 2 * PI * r(44)
        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        var shown = 0, target = 0, cap = 30, active = true, finishing = false, frame = null;
        var timers = [];

        root.classList.add('rr-loading');
        if (logo && logo.complete) logo.classList.add('is-ready');

        function later(fn, ms) { timers.push(setTimeout(fn, ms)); }
        function clearTimers() { timers.forEach(clearTimeout); timers = []; }
        function setStatus(message) { if (status.textContent !== message) status.textContent = message; }

        function paint() {
            var pct = Math.round(shown);
            arc.style.strokeDashoffset = (CIRCUMFERENCE * (1 - shown / 100)).toFixed(2);
            value.textContent = pct;
            el.setAttribute('aria-valuenow', pct);
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
                later(hide, reduceMotion ? 0 : 220);
                return;
            }
            frame = active ? requestAnimationFrame(tick) : null;
        }

        function start() {
            if (!frame) frame = requestAnimationFrame(tick);
        }

        function show(mode) {
            clearTimers();
            active = true; finishing = false;
            shown = target = 0; cap = 90;
            paint();
            setStatus(text[mode] || text.start);
            el.hidden = false;
            el.classList.remove('rr-loader--off', 'rr-loader--hint');
            root.classList.add('rr-loading');
            later(function () { el.classList.add('rr-loader--hint'); }, 2500);
            start();
        }

        function hide() {
            if (!active && el.classList.contains('rr-loader--off')) return;
            clearTimers();
            active = false; finishing = false;
            if (frame) { cancelAnimationFrame(frame); frame = null; }
            el.classList.add('rr-loader--off');
            root.classList.remove('rr-loading');
            later(function () {
                el.hidden = true;
                if (window.AOS && typeof window.AOS.refresh === 'function') window.AOS.refresh();
            }, 460);
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
                if (finishing) return;
                if (total === 0) { cap = 95; target = Math.max(target, 90); setStatus(text.finishing); return; }
                target = Math.max(target, 30 + 60 * (loaded / total));
                cap = Math.max(cap, Math.min(95, 30 + 60 * ((loaded + 1) / total)));
                setStatus(text.media.replace(':loaded', loaded).replace(':total', total));
            }

            images.forEach(function (img) {
                if (img.complete) { loaded++; return; }
                var done = function () { loaded++; update(); };
                img.addEventListener('load', done, { once: true });
                img.addEventListener('error', done, { once: true });
            });
            update();
        }

        setStatus(text.content);
        later(function () { el.classList.add('rr-loader--hint'); }, 2500);
        later(finish, 12000); // batas aman bila ada resource yang tidak pernah selesai
        start();

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { cap = 40; target = Math.max(target, 30); trackImages(); });
        } else {
            trackImages();
        }
        if (document.readyState === 'complete') finish();
        else window.addEventListener('load', finish);

        // Interaksi: klik/ketuk atau Esc untuk melewati, logo sedikit mengikuti kursor
        el.addEventListener('click', hide);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !el.hidden && !el.classList.contains('rr-loader--off')) hide();
        });
        if (!reduceMotion) {
            el.addEventListener('pointermove', function (e) {
                if (e.pointerType !== 'mouse') return;
                var x = (e.clientX / window.innerWidth - 0.5) * 16;
                var y = (e.clientY / window.innerHeight - 0.5) * -16;
                ring.style.transform = 'rotateX(' + y.toFixed(1) + 'deg) rotateY(' + x.toFixed(1) + 'deg)';
            });
            el.addEventListener('pointerleave', function () { ring.style.transform = ''; });
        }

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
        function showSoon(mode, isStillValid, safetyMs) {
            clearTimeout(pending);
            pending = setTimeout(function () {
                if (!isStillValid()) return;
                show(mode);
                later(hide, safetyMs); // mis. file diunduh dan halaman tidak berganti
            }, 150);
        }

        document.addEventListener('click', function (e) {
            var link = e.target.closest && e.target.closest('a[href]');
            if (!link || el.contains(link) || !sameSiteNavigation(link, e)) return;
            showSoon('navigating', function () { return !e.defaultPrevented; }, 10000);
        });

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (form.hasAttribute('data-no-loader') || (form.target && form.target !== '_self')) return;
            if ((form.getAttribute('method') || '').toLowerCase() === 'dialog') return;
            // Dicek setelah jeda: handler lain (AJAX) mungkin memanggil preventDefault belakangan
            showSoon('navigating', function () { return !e.defaultPrevented; }, 45000);
        });

        // Kembali lewat tombol Back (bfcache): jangan biarkan layar loading tertinggal
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) { clearTimeout(pending); hide(); }
        });
    })();
</script>
