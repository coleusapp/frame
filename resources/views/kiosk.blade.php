<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <title>Frame</title>
    <style>
        html, body { margin: 0; height: 100%; overflow: hidden; background: #000; }
        /* Every element, so no child's own cursor can show through; the transparent 1x1 image covers browsers without "none". */
        html, body, * {
            cursor: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=') 0 0, none !important;
        }
        #cursor-shield { position: fixed; top: 0; right: 0; bottom: 0; left: 0; z-index: 2147483647; background: transparent; }
        /* For 90 and 270 the screen's width and height trade places, so #screen is sized from the swapped viewport. */
        #screen {
            position: absolute; top: 50%; left: 50%; width: 100vw; height: 100vh;
            -webkit-transform: translate(-50%, -50%); transform: translate(-50%, -50%);
        }
        #screen.rotation-90 { width: 100vh; height: 100vw; -webkit-transform: translate(-50%, -50%) rotate(90deg); transform: translate(-50%, -50%) rotate(90deg); }
        #screen.rotation-180 { -webkit-transform: translate(-50%, -50%) rotate(180deg); transform: translate(-50%, -50%) rotate(180deg); }
        #screen.rotation-270 { width: 100vh; height: 100vw; -webkit-transform: translate(-50%, -50%) rotate(270deg); transform: translate(-50%, -50%) rotate(270deg); }
        #stage { position: absolute; overflow: hidden; }
        .artwork {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            object-fit: contain; opacity: 0;
            -webkit-transition: opacity 1.5s ease-in-out; transition: opacity 1.5s ease-in-out;
        }
        .artwork.is-visible { opacity: 1; }
        .caption {
            position: absolute; max-width: 60%;
            color: rgba(255, 255, 255, .85); font: 14px/1.4 -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            text-shadow: 0 1px 3px rgba(0, 0, 0, .9);
            opacity: 0; -webkit-transition: opacity 1.5s ease-in-out; transition: opacity 1.5s ease-in-out;
        }
        .caption.is-visible { opacity: 1; }
        .position-top-left, .position-top-center, .position-top-right { top: 20px; }
        .position-bottom-left, .position-bottom-center, .position-bottom-right { bottom: 20px; }
        .position-middle-left, .position-middle-right { top: 50%; -webkit-transform: translateY(-50%); transform: translateY(-50%); }
        .position-top-left, .position-middle-left, .position-bottom-left { left: 24px; text-align: left; }
        .position-top-right, .position-middle-right, .position-bottom-right { right: 24px; text-align: right; }
        .position-top-center, .position-bottom-center { left: 50%; text-align: center; -webkit-transform: translateX(-50%); transform: translateX(-50%); }
        .caption-title { font-style: italic; }
    </style>
</head>
<body style="background: {{ $backgroundColor }};">
    <div id="screen" class="rotation-{{ $rotation }}">
        <div id="stage" style="top: {{ $inset['top'] }}px; right: {{ $inset['right'] }}px; bottom: {{ $inset['bottom'] }}px; left: {{ $inset['left'] }}px;">
            <img class="artwork" alt="">
            <img class="artwork" alt="">
            <div class="caption position-bottom-left"><div class="caption-title"></div><div class="caption-meta"></div></div>
        </div>
    </div>
    {{-- Topmost, so the pointer is always over this element (never an image or text, which could pick their own cursor or be selected). --}}
    <div id="cursor-shield"></div>

    <script>
        (function () {
            var NEXT_URL = @json($nextUrl, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
            var RETRY_MS = 60 * 1000;
            var RELOAD_MS = 24 * 60 * 60 * 1000;

            var screenElement = document.getElementById('screen');
            var stage = document.getElementById('stage');
            var layers = document.querySelectorAll('.artwork');
            var caption = document.querySelector('.caption');
            var captionTitle = document.querySelector('.caption-title');
            var captionMeta = document.querySelector('.caption-meta');
            var front = 0;
            var currentId = null;

            function schedule(ms) {
                window.setTimeout(next, ms);
            }

            function applyInset(inset) {
                if (!inset) {
                    return;
                }

                stage.style.top = inset.top + 'px';
                stage.style.right = inset.right + 'px';
                stage.style.bottom = inset.bottom + 'px';
                stage.style.left = inset.left + 'px';
            }

            function show(artwork, src) {
                var back = 1 - front;

                applyInset(artwork.inset);
                document.body.style.background = artwork.background_color || '';
                screenElement.className = 'rotation-' + (artwork.rotation || 0);
                layers[back].src = src;
                layers[back].className = 'artwork is-visible';
                layers[front].className = 'artwork';
                front = back;

                captionTitle.textContent = artwork.title || '';
                captionMeta.textContent = [artwork.artist, artwork.date].filter(Boolean).join(', ');
                caption.className = 'caption position-' + (artwork.caption_position || 'bottom-left') + (artwork.show_caption ? ' is-visible' : '');

                currentId = artwork.id;
                schedule(Math.max(5, artwork.interval_seconds || 60) * 1000);
            }

            function preload(artwork) {
                var image = new Image();

                image.onload = function () { show(artwork, artwork.image_url); };
                image.onerror = function () { schedule(RETRY_MS); };
                image.src = artwork.image_url;
            }

            function next() {
                var request = new XMLHttpRequest();
                var url = NEXT_URL + (currentId ? '?current=' + encodeURIComponent(currentId) : '');

                request.open('GET', url, true);
                request.setRequestHeader('Accept', 'application/json');
                request.timeout = 30000;
                request.onload = function () {
                    if (request.status !== 200) {
                        schedule(RETRY_MS);

                        return;
                    }

                    try {
                        preload(JSON.parse(request.responseText));
                    } catch (error) {
                        schedule(RETRY_MS);
                    }
                };
                request.onerror = request.ontimeout = function () { schedule(RETRY_MS); };
                request.send();
            }

            window.setTimeout(function () { window.location.reload(); }, RELOAD_MS);
            next();
        })();
    </script>
</body>
</html>
