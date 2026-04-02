<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Задачи') — Планировщик</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:          #f5f0e8;
            --bg-card:     #fffdf8;
            --ink:         #1a1a1a;
            --ink-muted:   #6b6560;
            --accent:      #b44d2d;
            --accent-hover:#933c22;
            --border:      #ddd5c8;
            --radius:      8px;

            --clr-new:         #d4a34a;
            --clr-new-bg:      #fdf5e6;
            --clr-progress:    #4a7fb5;
            --clr-progress-bg: #e8f0fa;
            --clr-done:        #4a9e6f;
            --clr-done-bg:     #e6f5ed;

            --font-display: 'Playfair Display', Georgia, serif;
            --font-body:    'Inter', system-ui, sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--ink);
            line-height: 1.6;
            min-height: 100vh;
        }

        .site-header {
            background: var(--ink);
            color: var(--bg);
            padding: 1.25rem 0;
        }
        .site-header .wrap {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 1rem;
        }
        .site-header h1 {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .site-header h1 a { color: inherit; text-decoration: none; }
        .site-header nav a {
            color: var(--bg);
            opacity: .75;
            text-decoration: none;
            font-size: .9rem;
            transition: opacity .2s;
        }
        .site-header nav a:hover { opacity: 1; }

        .wrap {
            max-width: 860px;
            margin: 0 auto;
            padding: 0 1.25rem;
        }

        main { padding: 2rem 0 4rem; }

        .page-title {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 1.5rem;
        }

        /* Flash messages */
        .flash {
            background: var(--clr-done-bg);
            color: var(--clr-done);
            border: 1px solid var(--clr-done);
            padding: .75rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-size: .9rem;
            animation: flashIn .3s ease;
        }
        @keyframes flashIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Status chips */
        .status-chip {
            display: inline-block;
            padding: .2em .7em;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            white-space: nowrap;
            line-height: 1.4;
        }
        .status-new        { background: var(--clr-new-bg);      color: var(--clr-new); }
        .status-progress   { background: var(--clr-progress-bg); color: var(--clr-progress); }
        .status-done       { background: var(--clr-done-bg);     color: var(--clr-done); }

        /* Cards & list */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: box-shadow .2s, transform .15s;
        }
        .card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
            transform: translateY(-1px);
        }
        .card-title {
            font-family: var(--font-display);
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: .35rem;
        }
        .card-title a {
            color: var(--ink);
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color .2s;
        }
        .card-title a:hover { border-color: var(--accent); }
        .card-meta {
            font-size: .82rem;
            color: var(--ink-muted);
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
        }

        /* Toolbar: filter / search */
        .toolbar {
            display: flex;
            gap: .75rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .toolbar input[type="text"],
        .toolbar select {
            font-family: var(--font-body);
            font-size: .9rem;
            padding: .5rem .75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--bg-card);
            color: var(--ink);
            transition: border-color .2s;
        }
        .toolbar input[type="text"]:focus,
        .toolbar select:focus {
            outline: none;
            border-color: var(--accent);
        }
        .toolbar input[type="text"] { flex: 1; min-width: 180px; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-family: var(--font-body);
            font-size: .9rem;
            font-weight: 500;
            padding: .55rem 1.1rem;
            border-radius: var(--radius);
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background .2s, transform .1s;
            line-height: 1.4;
        }
        .btn:active { transform: scale(.97); }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }
        .btn-primary:hover { background: var(--accent-hover); }

        .btn-secondary {
            background: transparent;
            color: var(--ink);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover { background: var(--bg); }

        .btn-danger {
            background: transparent;
            color: #c0392b;
            border: 1px solid #e8c4c0;
            font-size: .82rem;
        }
        .btn-danger:hover { background: #fdf0ee; }

        .btn-sm { padding: .35rem .7rem; font-size: .82rem; }

        /* Forms */
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: .35rem;
            font-size: .9rem;
        }
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            font-family: var(--font-body);
            font-size: .95rem;
            padding: .6rem .8rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--bg-card);
            color: var(--ink);
            transition: border-color .2s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent);
        }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .form-error {
            color: #c0392b;
            font-size: .82rem;
            margin-top: .3rem;
        }

        /* Task detail */
        .task-detail .description {
            margin: 1.25rem 0;
            white-space: pre-wrap;
            line-height: 1.7;
        }
        .task-detail .meta-bar {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            font-size: .85rem;
            color: var(--ink-muted);
            margin-bottom: 1.5rem;
        }
        .task-actions {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        /* Pagination */
        .pagination-wrap {
            display: flex;
            justify-content: center;
            gap: .35rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .pagination-wrap a,
        .pagination-wrap span {
            display: inline-block;
            padding: .4rem .75rem;
            border-radius: var(--radius);
            font-size: .85rem;
            text-decoration: none;
            border: 1px solid var(--border);
            color: var(--ink);
            transition: background .2s;
        }
        .pagination-wrap a:hover { background: var(--bg-card); }
        .pagination-wrap .active {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }
        .pagination-wrap .disabled { opacity: .4; pointer-events: none; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--ink-muted);
        }
        .empty-state .icon { font-size: 2.5rem; margin-bottom: .75rem; }
        .empty-state p { font-size: 1.05rem; }

        .site-footer {
            text-align: center;
            padding: 2rem 0;
            font-size: .8rem;
            color: var(--ink-muted);
            border-top: 1px solid var(--border);
        }

        @media (max-width: 600px) {
            .page-title { font-size: 1.5rem; }
            .card { padding: 1rem; }
            .toolbar { flex-direction: column; }
            .toolbar input[type="text"] { min-width: 100%; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="wrap">
            <h1><a href="{{ route('tasks.index') }}">Планировщик задач</a></h1>
            <nav>
                <a href="{{ route('tasks.create') }}">+ Новая задача</a>
            </nav>
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
</body>
</html>
