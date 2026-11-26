<?php
require_once __DIR__ . '/includes/bootstrap.php';

// Logout is a state-changing action: accept POST only and validate CSRF first.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect();
}
verify_csrf();

$user = current_user();
$userName = trim((string)($user['nama'] ?? 'Pengguna'));

// Invalidate the authenticated session immediately, before the animation begins.
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}
session_destroy();

// Prevent browsers/proxies from restoring an authenticated or stale page from cache.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$loginUrl = url();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f3f4f8">
    <meta http-equiv="refresh" content="6;url=<?= e($loginUrl) ?>">
    <title>Sampai Jumpa · SIM SCREENSHOT</title>
    <link rel="preload" as="image" href="<?= e(url('assets/images/logo-screenshot-mark-white-transparent.png')) ?>">
    <style>
        :root {
            color-scheme: light;
            --reveal-red: #ef4058;
            --reveal-blue: #459bff;
            --reveal-yellow: #f4cf55;
            --reveal-white: #f7f8fc;
        }

        * { box-sizing: border-box; }
        html, body { min-height: 100%; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            overflow: hidden;
            color: #fff;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            background:
                radial-gradient(ellipse at 14% 18%, rgba(239,64,88,.10), transparent 34%),
                radial-gradient(ellipse at 86% 82%, rgba(69,155,255,.11), transparent 34%),
                radial-gradient(ellipse at 52% 50%, rgba(244,207,85,.08), transparent 44%),
                #f3f4f8;
        }

        .welcome-stage {
            position: relative;
            width: min(620px, 100%);
            isolation: isolate;
        }

        .welcome-stage::before,
        .welcome-stage::after {
            content: "";
            position: absolute;
            z-index: -1;
            border-radius: 50%;
            pointer-events: none;
        }

        .welcome-stage::before {
            width: 240px;
            height: 240px;
            top: -55px;
            left: -65px;
            background: radial-gradient(circle, rgba(239,64,88,.20), transparent 70%);
            animation: ambient-drift 5s ease-in-out infinite alternate;
        }

        .welcome-stage::after {
            width: 280px;
            height: 280px;
            right: -70px;
            bottom: -65px;
            background: radial-gradient(circle, rgba(69,155,255,.20), transparent 70%);
            animation: ambient-drift 6s ease-in-out -2s infinite alternate-reverse;
        }

        .welcome-card {
            position: relative;
            min-height: 490px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            isolation: isolate;
            padding: 52px 30px 42px;
            border: 1px solid rgba(255,255,255,.16);
            border-radius: 28px;
            background:
                radial-gradient(circle at 50% 42%, rgba(69,155,255,.10), transparent 38%),
                radial-gradient(circle at 16% 85%, rgba(239,64,88,.13), transparent 34%),
                linear-gradient(145deg, #20232d 0%, #292d39 54%, #191c25 100%);
            box-shadow:
                0 35px 90px rgba(23,28,42,.22),
                0 10px 30px rgba(23,28,42,.12),
                inset 0 1px 0 rgba(255,255,255,.12);
            animation: card-arrive 650ms cubic-bezier(.18,.85,.25,1) both;
        }

        .welcome-card::before {
            content: "";
            position: absolute;
            z-index: -1;
            inset: 12px;
            border: 1px solid rgba(255,255,255,.055);
            border-radius: 20px;
            pointer-events: none;
        }

        .welcome-orbit {
            position: absolute;
            z-index: 0;
            inset: 0;
            width: 100%;
            height: 100%;
            overflow: visible;
            pointer-events: none;
        }

        .welcome-orbit path {
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            vector-effect: non-scaling-stroke;
        }

        .orbit-red {
            stroke: var(--reveal-red);
            stroke-width: 3;
            stroke-dasharray: 180 820;
            animation: orbit-flow 2.2s linear infinite;
            filter: drop-shadow(0 0 5px rgba(239,64,88,.65));
        }

        .orbit-blue {
            stroke: var(--reveal-blue);
            stroke-width: 3;
            stroke-dasharray: 150 850;
            animation: orbit-flow 2.7s linear -1s infinite;
            filter: drop-shadow(0 0 5px rgba(69,155,255,.65));
        }

        .orbit-yellow {
            stroke: var(--reveal-yellow);
            stroke-width: 2.6;
            stroke-dasharray: 125 875;
            animation: orbit-flow 3s linear -1.7s infinite;
            filter: drop-shadow(0 0 5px rgba(244,207,85,.55));
        }

        .orbit-white {
            stroke: rgba(255,255,255,.76);
            stroke-width: 1.6;
            stroke-dasharray: 70 930;
            animation: orbit-flow 3.4s linear -2s infinite;
            opacity: .7;
        }

        .brand-mark-wrap {
            position: relative;
            z-index: 2;
            width: 174px;
            height: 174px;
            display: grid;
            place-items: center;
            margin-bottom: 20px;
            perspective: 900px;
        }

        .brand-mark-wrap::before {
            content: "";
            position: absolute;
            inset: 15px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,.16), rgba(69,155,255,.07) 48%, transparent 72%);
            box-shadow: 0 0 42px rgba(69,155,255,.10), 0 0 55px rgba(239,64,88,.08);
            animation: mark-aura 2.4s ease-in-out infinite alternate;
        }

        .brand-mark {
            position: relative;
            z-index: 1;
            display: block;
            width: 142px;
            height: 142px;
            object-fit: contain;
            transform-origin: 50% 68%;
            opacity: 0;
            filter:
                drop-shadow(0 3px 0 rgba(255,255,255,.25))
                drop-shadow(0 11px 5px rgba(0,0,0,.36))
                drop-shadow(0 0 18px rgba(255,255,255,.13));
            animation: logo-emboss 1050ms cubic-bezier(.16,1,.3,1) 120ms forwards;
        }

        .welcome-wordmark {
            position: relative;
            z-index: 2;
            margin: 0;
            color: #fff;
            font-size: clamp(22px, 4vw, 30px);
            font-weight: 850;
            letter-spacing: .19em;
            text-indent: .19em;
            text-align: center;
            text-shadow: 0 2px 0 rgba(255,255,255,.14), 0 7px 18px rgba(0,0,0,.35);
            opacity: 0;
            animation: text-rise 600ms ease-out 650ms forwards;
        }

        .welcome-subtitle {
            position: relative;
            z-index: 2;
            margin: 11px 0 0;
            color: rgba(255,255,255,.68);
            font-size: 10px;
            letter-spacing: .22em;
            text-align: center;
            text-transform: uppercase;
            opacity: 0;
            animation: text-rise 600ms ease-out 780ms forwards;
        }

        .welcome-greeting {
            position: relative;
            z-index: 2;
            margin: 28px 0 0;
            color: rgba(255,255,255,.92);
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            opacity: 0;
            animation: text-rise 650ms ease-out 900ms forwards;
        }

        .welcome-caption {
            position: relative;
            z-index: 2;
            margin: 8px 0 0;
            color: rgba(255,255,255,.56);
            font-size: 12px;
            text-align: center;
            opacity: 0;
            animation: text-rise 650ms ease-out 1020ms forwards;
        }

        .welcome-progress {
            position: relative;
            z-index: 2;
            width: min(220px, 72%);
            height: 3px;
            margin-top: 32px;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(255,255,255,.13);
        }

        .welcome-progress span {
            display: block;
            width: 100%;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--reveal-red), var(--reveal-blue), var(--reveal-yellow));
            transform: scaleX(0);
            transform-origin: left center;
            animation: progress-fill 4900ms cubic-bezier(.2,.7,.25,1) 100ms forwards;
        }

        .welcome-skip {
            position: relative;
            z-index: 3;
            margin-top: 18px;
            color: rgba(255,255,255,.66);
            font-size: 11px;
            text-decoration: none;
            transition: color 180ms ease;
        }

        .welcome-skip:hover,
        .welcome-skip:focus-visible {
            color: #fff;
            text-decoration: underline;
            outline: none;
        }

        @keyframes card-arrive {
            from { opacity: 0; transform: translateY(18px) scale(.975); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes logo-emboss {
            0% { opacity: 0; transform: rotateX(34deg) rotateY(-13deg) translateY(24px) scale(.58); }
            58% { opacity: 1; transform: rotateX(-7deg) rotateY(4deg) translateY(-5px) scale(1.09); }
            78% { transform: rotateX(3deg) rotateY(-2deg) translateY(2px) scale(.985); }
            100% { opacity: 1; transform: rotateX(0) rotateY(0) translateY(0) scale(1); }
        }

        @keyframes text-rise {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes orbit-flow {
            from { stroke-dashoffset: 1000; }
            to { stroke-dashoffset: 0; }
        }

        @keyframes mark-aura {
            from { transform: scale(.92); opacity: .62; }
            to { transform: scale(1.08); opacity: 1; }
        }

        @keyframes progress-fill {
            to { transform: scaleX(1); }
        }

        @keyframes ambient-drift {
            from { transform: translate3d(-8px, 5px, 0) scale(.94); opacity: .65; }
            to { transform: translate3d(10px, -8px, 0) scale(1.06); opacity: 1; }
        }

        @media (max-width: 520px) {
            body { padding: 14px; }
            .welcome-card { min-height: 430px; padding: 40px 20px 32px; border-radius: 22px; }
            .brand-mark-wrap { width: 148px; height: 148px; }
            .brand-mark { width: 122px; height: 122px; }
            .welcome-wordmark { letter-spacing: .13em; text-indent: .13em; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>
    <main class="welcome-stage" aria-labelledby="welcome-title">
        <section class="welcome-card" aria-label="Animasi keluar SIM SCREENSHOT">
            <svg class="welcome-orbit" viewBox="0 0 620 490" preserveAspectRatio="none" aria-hidden="true">
                <path class="orbit-red" pathLength="1000" d="M-45 245 C55 30 220 55 310 170 S520 400 665 220"/>
                <path class="orbit-blue" pathLength="1000" d="M-45 350 C80 470 190 340 310 245 S510 20 665 160"/>
                <path class="orbit-yellow" pathLength="1000" d="M-45 100 C100 230 200 15 325 125 S520 470 665 355"/>
                <path class="orbit-white" pathLength="1000" d="M-45 420 C95 300 170 465 310 325 S520 90 665 35"/>
            </svg>

            <div class="brand-mark-wrap">
                <img class="brand-mark"
                     src="<?= e(url('assets/images/logo-screenshot-mark-white-transparent.png')) ?>"
                     alt="Logo SCREENSHOT">
            </div>

            <h1 class="welcome-wordmark" id="welcome-title">SIM SCREENSHOT</h1>
            <p class="welcome-subtitle">Sistem Informasi Manajemen</p>
            <p class="welcome-greeting">Terima kasih, <?= e($userName) ?></p>
            <p class="welcome-caption">Anda telah keluar dari sistem dengan aman.</p>

            <div class="welcome-progress" role="progressbar" aria-label="Kembali ke halaman login">
                <span></span>
            </div>

            <a class="welcome-skip" href="<?= e($loginUrl) ?>">Kembali ke login sekarang →</a>
        </section>
    </main>

    <script>
        (() => {
            const destination = <?= json_encode($loginUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            const reducedMotion = window.matchMedia &&
                window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const delay = reducedMotion ? 350 : 5000;

            window.setTimeout(() => {
                window.location.replace(destination);
            }, delay);
        })();
    </script>
</body>
</html>
