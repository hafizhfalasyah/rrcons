{{--
    Pelacak kunjungan (first-party, tanpa cookie tambahan). Variabel opsional: $article.
    Kunjungan hanya tercatat bila browser menjalankan skrip ini, sehingga bot & scraper tidak ikut terhitung.
--}}
@if (\App\Support\Analytics\Tracker::enabled())
@php
    $analyticsToken = \App\Support\Analytics\Tracker::issue(request(), $article ?? null);
    $analyticsConfig = [
        'token' => $analyticsToken['token'],
        'view' => $analyticsToken['view'],
        'hit' => route('analytics.hit'),
        'ping' => route('analytics.ping'),
        'interval' => config('analytics.heartbeat_interval') * 1000,
        'bots' => config('analytics.bot_pattern'),
    ];
@endphp
<script>
    (function () {
        var config = @json($analyticsConfig);

        if (navigator.webdriver || new RegExp(config.bots, 'i').test(navigator.userAgent)) {
            return;
        }

        var viewId = config.view;
        var engaged = 0;
        var lastTick = Date.now();
        var visible = document.visibilityState === 'visible';

        function send(url, data, useBeacon) {
            var body = JSON.stringify(data);

            if (useBeacon && navigator.sendBeacon && navigator.sendBeacon(url, new Blob([body], { type: 'text/plain' }))) {
                return;
            }

            if (window.fetch) {
                fetch(url, { method: 'POST', body: body, keepalive: true, credentials: 'same-origin', headers: { 'Content-Type': 'text/plain' } })
                    .catch(function () {});
            }
        }

        function tick() {
            var now = Date.now();
            if (visible) {
                engaged += (now - lastTick) / 1000;
            }
            lastTick = now;
        }

        function ping(useBeacon) {
            tick();
            send(config.ping, { v: viewId, e: Math.round(engaged) }, useBeacon);
        }

        var params = new URLSearchParams(window.location.search);

        send(config.hit, {
            t: config.token,
            ref: document.referrer,
            lang: navigator.language || '',
            sw: window.screen ? window.screen.width : null,
            us: params.get('utm_source'),
            um: params.get('utm_medium'),
            uc: params.get('utm_campaign')
        }, false);

        // heartbeat hanya saat tab terlihat
        setInterval(function () {
            if (visible) {
                ping(false);
            }
        }, config.interval);

        document.addEventListener('visibilitychange', function () {
            tick();
            visible = document.visibilityState === 'visible';
            if (!visible) {
                ping(true);
            }
        });

        window.addEventListener('pagehide', function () {
            ping(true);
        });
    })();
</script>
@endif
