<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Задачи') — Планировщик</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Prata&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f4efe7;
            --bg-soft: #fcf8f2;
            --surface: rgba(255, 251, 246, 0.78);
            --surface-strong: #fffaf4;
            --ink: #191613;
            --ink-muted: #6f665d;
            --accent: #9f5536;
            --accent-hover: #82452c;
            --accent-soft: rgba(159, 85, 54, 0.14);
            --border: rgba(98, 76, 58, 0.18);
            --shadow: 0 22px 48px rgba(43, 27, 14, 0.08);
            --radius: 18px;

            --clr-new: #b57c24;
            --clr-new-bg: #fbf1da;
            --clr-progress: #355d95;
            --clr-progress-bg: #e7eef9;
            --clr-done: #2f7b58;
            --clr-done-bg: #e5f3eb;

            --font-display: 'Prata', Georgia, serif;
            --font-body: 'Manrope', system-ui, sans-serif;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--font-body);
            color: var(--ink);
            line-height: 1.65;
            background:
                radial-gradient(circle at top right, rgba(159, 85, 54, 0.16), transparent 24rem),
                radial-gradient(circle at bottom left, rgba(53, 93, 149, 0.10), transparent 20rem),
                linear-gradient(180deg, #f8f3ec 0%, #f2ece4 100%);
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                repeating-linear-gradient(
                    180deg,
                    rgba(255, 255, 255, 0.15) 0,
                    rgba(255, 255, 255, 0.15) 1px,
                    transparent 1px,
                    transparent 34px
                );
            opacity: 0.18;
        }

        a {
            color: inherit;
        }

        .wrap {
            width: min(100%, 980px);
            margin: 0 auto;
            padding: 0 1.25rem;
        }

        .site-header {
            padding: 1.4rem 0 1.15rem;
            background: rgba(20, 17, 15, 0.88);
            color: #f7f1e8;
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
            z-index: 2;
        }

        .brand-bar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1.25rem;
            align-items: end;
        }

        .brand-kicker,
        .section-kicker {
            margin: 0 0 0.45rem;
            text-transform: uppercase;
            letter-spacing: 0.24em;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .brand-kicker {
            color: rgba(247, 241, 232, 0.62);
        }

        .section-kicker {
            color: var(--accent);
        }

        .brand-title {
            margin: 0;
            font-family: var(--font-display);
            font-size: clamp(1.55rem, 3vw, 2rem);
            letter-spacing: -0.03em;
            line-height: 1.15;
        }

        .brand-title a {
            text-decoration: none;
        }

        .brand-note {
            max-width: 18rem;
            margin: 0;
            text-align: right;
            color: rgba(247, 241, 232, 0.74);
            font-size: 0.93rem;
        }

        main {
            padding: 2.1rem 0 4rem;
            position: relative;
            z-index: 1;
        }

        .page-hero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1rem 1.5rem;
            align-items: end;
            margin-bottom: 1.4rem;
        }

        .page-title {
            margin: 0 0 0.8rem;
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 3rem);
            letter-spacing: -0.03em;
            line-height: 1.08;
        }

        .page-title-tight {
            margin-bottom: 0.45rem;
        }

        .page-lead {
            margin: 0;
            max-width: 42rem;
            color: var(--ink-muted);
            font-size: 1rem;
        }

        .panel,
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
        }

        .panel {
            padding: 1rem;
        }

        .panel-soft {
            background: rgba(255, 250, 244, 0.58);
        }

        .hero-stat {
            min-width: 9rem;
            text-align: center;
            padding: 1rem 1.1rem;
        }

        .hero-stat-value {
            display: block;
            font-family: var(--font-display);
            font-size: 2.2rem;
            line-height: 1;
            margin-bottom: 0.3rem;
        }

        .hero-stat-label {
            display: block;
            color: var(--ink-muted);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
        }

        .flash {
            margin-bottom: 1.4rem;
            padding: 0.95rem 1rem;
            border-radius: 16px;
            border: 1px solid rgba(47, 123, 88, 0.34);
            background: linear-gradient(135deg, rgba(229, 243, 235, 0.95), rgba(245, 251, 246, 0.95));
            color: var(--clr-done);
            animation: flashIn 0.28s ease;
        }

        @keyframes flashIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .toolbar-panel {
            padding: 1rem;
            margin-bottom: 1.4rem;
        }

        .toolbar-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.8rem;
        }

        .results-status {
            min-height: 1.2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            color: var(--ink-muted);
            font-size: 0.83rem;
        }

        .results-status[data-loading="true"]::before {
            content: '';
            width: 0.72rem;
            height: 0.72rem;
            border-radius: 50%;
            border: 2px solid rgba(159, 85, 54, 0.18);
            border-top-color: var(--accent);
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(11rem, 0.8fr) auto auto;
            gap: 0.75rem;
            align-items: center;
        }

        .toolbar input[type="text"],
        .toolbar select,
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.78rem 0.9rem;
            border-radius: 14px;
            border: 1px solid rgba(98, 76, 58, 0.16);
            background: rgba(255, 255, 255, 0.72);
            color: var(--ink);
            font: inherit;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .toolbar input[type="text"]:focus,
        .toolbar select:focus,
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: rgba(159, 85, 54, 0.46);
            box-shadow: 0 0 0 4px rgba(159, 85, 54, 0.10);
            background: rgba(255, 255, 255, 0.92);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border-radius: 999px;
            padding: 0.78rem 1.2rem;
            border: 1px solid transparent;
            text-decoration: none;
            font: inherit;
            font-size: 0.94rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.14s ease, background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #bf6d46);
            color: #fff;
            box-shadow: 0 12px 24px rgba(159, 85, 54, 0.18);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-hover), var(--accent));
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.55);
            border-color: rgba(98, 76, 58, 0.16);
            color: var(--ink);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.9);
        }

        .btn-danger {
            background: rgba(255, 246, 244, 0.72);
            border-color: rgba(192, 57, 43, 0.20);
            color: #b73c30;
        }

        .btn-danger:hover {
            background: rgba(255, 240, 236, 0.92);
        }

        .btn-sm {
            padding: 0.72rem 0.95rem;
            font-size: 0.88rem;
        }

        .results-shell {
            transition: opacity 0.22s ease;
        }

        .results-shell.is-loading {
            opacity: 0.56;
        }

        .results-summary {
            margin-bottom: 0.9rem;
            color: var(--ink-muted);
            font-size: 0.86rem;
        }

        .card {
            position: relative;
            overflow: hidden;
            padding: 1.35rem 1.4rem;
            margin-bottom: 1rem;
        }

        .card::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, rgba(159, 85, 54, 0.86), rgba(53, 93, 149, 0.46));
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card-heading {
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            align-items: flex-start;
        }

        .card-title {
            margin: 0 0 0.45rem;
            font-family: var(--font-display);
            font-size: 1.25rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .card-title a {
            text-decoration: none;
            background-image: linear-gradient(currentColor, currentColor);
            background-repeat: no-repeat;
            background-position: 0 100%;
            background-size: 0 1px;
            transition: background-size 0.22s ease;
        }

        .card-title a:hover {
            background-size: 100% 1px;
        }

        .card-stamp {
            white-space: nowrap;
            color: rgba(25, 22, 19, 0.34);
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            padding-top: 0.15rem;
        }

        .card-meta,
        .task-detail .meta-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            font-size: 0.84rem;
            color: var(--ink-muted);
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 0.28rem 0.78rem;
            font-size: 0.79rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .status-new {
            background: var(--clr-new-bg);
            color: var(--clr-new);
        }

        .status-progress {
            background: var(--clr-progress-bg);
            color: var(--clr-progress);
        }

        .status-done {
            background: var(--clr-done-bg);
            color: var(--clr-done);
        }

        .task-quote {
            margin: 1rem 0 0;
            padding: 0.15rem 0 0.15rem 1rem;
            border-left: 2px solid rgba(159, 85, 54, 0.24);
            color: var(--ink-muted);
            font-size: 0.96rem;
            font-style: italic;
        }

        .empty-state {
            padding: 2.8rem 1.4rem;
            text-align: center;
            color: var(--ink-muted);
        }

        .empty-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3.4rem;
            height: 3.4rem;
            margin-bottom: 1rem;
            border-radius: 50%;
            border: 1px solid rgba(98, 76, 58, 0.16);
            background: rgba(255, 255, 255, 0.72);
            font-family: var(--font-display);
            font-size: 1.2rem;
            color: var(--accent);
        }

        .empty-state p {
            margin: 0 0 1rem;
            font-size: 1.05rem;
        }

        .pagination-summary {
            margin-top: 1.2rem;
            text-align: center;
            color: var(--ink-muted);
            font-size: 0.84rem;
        }

        .pagination-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.45rem;
            margin-top: 0.95rem;
        }

        .pagination-wrap a,
        .pagination-wrap span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.6rem;
            min-height: 2.6rem;
            padding: 0.4rem 0.75rem;
            border-radius: 999px;
            border: 1px solid rgba(98, 76, 58, 0.16);
            text-decoration: none;
            color: var(--ink);
            background: rgba(255, 255, 255, 0.56);
            transition: background 0.2s ease, transform 0.14s ease;
        }

        .pagination-wrap a:hover {
            background: rgba(255, 255, 255, 0.92);
            transform: translateY(-1px);
        }

        .pagination-wrap .active {
            background: linear-gradient(135deg, var(--accent), #bf6d46);
            color: #fff;
            border-color: transparent;
        }

        .pagination-wrap .disabled {
            opacity: 0.35;
            pointer-events: none;
        }

        .form-group {
            margin-bottom: 1.15rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.92rem;
            font-weight: 600;
        }

        .form-group textarea {
            min-height: 140px;
            resize: vertical;
        }

        .form-error {
            margin-top: 0.35rem;
            color: #b73c30;
            font-size: 0.83rem;
        }

        .task-detail .description {
            margin: 1.2rem 0 1.4rem;
            white-space: pre-wrap;
            line-height: 1.8;
        }

        .description-empty {
            margin: 1rem 0 1.4rem;
            color: var(--ink-muted);
        }

        .task-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
        }

        .site-footer {
            padding: 2.2rem 0 2.8rem;
            color: var(--ink-muted);
            text-align: center;
            font-size: 0.82rem;
        }

        @media (max-width: 760px) {
            .brand-bar,
            .page-hero,
            .toolbar {
                grid-template-columns: 1fr;
            }

            .brand-note {
                max-width: none;
                text-align: left;
            }

            .toolbar-heading {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero-stat {
                justify-self: start;
            }
        }

        @media (max-width: 560px) {
            .page-title {
                font-size: 2rem;
            }

            .card {
                padding: 1.15rem 1rem;
            }

            .card-heading {
                flex-direction: column;
            }

            .card-stamp {
                padding-top: 0;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="wrap brand-bar">
            <div>
                <p class="brand-kicker">Порядок без шума</p>
                <h1 class="brand-title"><a href="{{ route('tasks.index') }}">Планировщик задач</a></h1>
            </div>
            <p class="brand-note">Спокойный интерфейс для дел, заметок и коротких решений на день.</p>
        </div>
    </header>

    <main>
        <div class="wrap">
            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="site-footer">
        <div class="wrap">&copy; {{ date('Y') }} Планировщик задач</div>
    </footer>

    @stack('scripts')
</body>
</html>
