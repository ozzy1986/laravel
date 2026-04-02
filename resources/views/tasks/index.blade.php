@extends('layouts.app')

@section('title', 'Список задач')

@section('content')
    <section class="page-hero">
        <h2 class="page-title page-title-tight">Задачи</h2>
        <aside class="hero-stat panel panel-soft">
            <span class="hero-stat-value" data-results-total>{{ $tasks->total() }}</span>
            <span class="hero-stat-label">всего</span>
        </aside>
    </section>

    <section class="toolbar-panel panel">
        <div class="toolbar-heading">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">Создать задачу</a>
            <span class="results-status" data-results-status aria-live="polite"></span>
        </div>

        <form method="GET" action="{{ route('tasks.index') }}" class="toolbar" data-task-filters>
            <input
                type="text"
                name="search"
                value="{{ $filters['search'] ?? '' }}"
                placeholder="Найти по названию или описанию"
                autocomplete="off"
            >

            <select name="status">
                <option value="">Все статусы</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-secondary btn-sm">Применить</button>

            <a
                href="{{ route('tasks.index') }}"
                class="btn btn-secondary btn-sm"
                data-reset-filters
                @if(empty($filters['search']) && empty($filters['status'])) hidden @endif
            >Сбросить</a>
        </form>
    </section>

    <div class="results-shell" data-task-results aria-live="polite">
        @include('tasks._results')
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('[data-task-filters]');
            const results = document.querySelector('[data-task-results]');
            const statusNote = document.querySelector('[data-results-status]');
            const totalNote = document.querySelector('[data-results-total]');

            if (!form || !results || !window.fetch || !window.URL) {
                return;
            }

            const searchInput = form.querySelector('input[name="search"]');
            const statusSelect = form.querySelector('select[name="status"]');
            const resetLink = form.querySelector('[data-reset-filters]');
            let debounceTimer = null;
            let statusTimer = null;
            let requestController = null;

            const setLoadingState = (loading, message = '') => {
                results.classList.toggle('is-loading', loading);
                results.setAttribute('aria-busy', loading ? 'true' : 'false');

                if (!statusNote) {
                    return;
                }

                statusNote.dataset.loading = loading ? 'true' : 'false';
                statusNote.textContent = message;
            };

            const syncResetLinkState = () => {
                if (!resetLink) {
                    return;
                }

                const hasFilters = searchInput.value.trim() !== '' || statusSelect.value.trim() !== '';
                resetLink.hidden = !hasFilters;
            };

            const scheduleStatusClear = () => {
                if (!statusNote) {
                    return;
                }

                window.clearTimeout(statusTimer);
                statusTimer = window.setTimeout(() => {
                    statusNote.dataset.loading = 'false';
                    statusNote.textContent = '';
                }, 1200);
            };

            const syncFormWithUrl = (urlString) => {
                const url = new URL(urlString, window.location.origin);

                searchInput.value = url.searchParams.get('search') ?? '';
                statusSelect.value = url.searchParams.get('status') ?? '';
            };

            const buildUrlFromForm = () => {
                const url = new URL(form.action, window.location.origin);
                const searchValue = searchInput.value.trim();
                const statusValue = statusSelect.value.trim();

                if (searchValue !== '') {
                    url.searchParams.set('search', searchValue);
                }

                if (statusValue !== '') {
                    url.searchParams.set('status', statusValue);
                }

                return url.toString();
            };

            const fetchResults = (urlString, { syncForm = false } = {}) => {
                if (syncForm) {
                    syncFormWithUrl(urlString);
                }

                if (requestController) {
                    requestController.abort();
                }

                requestController = new AbortController();

                setLoadingState(true, '');

                fetch(urlString, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: requestController.signal,
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('Request failed');
                        }

                        return response.json();
                    })
                    .then((data) => {
                        results.innerHTML = data.html;
                        if (totalNote && typeof data.total !== 'undefined') {
                            totalNote.textContent = data.total;
                        }
                        window.history.replaceState({}, '', urlString);
                        syncResetLinkState();
                        setLoadingState(false, '');
                        scheduleStatusClear();
                    })
                    .catch((error) => {
                        if (error.name === 'AbortError') {
                            return;
                        }

                        window.location.href = urlString;
                    });
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                fetchResults(buildUrlFromForm());
            });

            searchInput.addEventListener('input', () => {
                syncResetLinkState();
                window.clearTimeout(debounceTimer);

                debounceTimer = window.setTimeout(() => {
                    fetchResults(buildUrlFromForm());
                }, 280);
            });

            statusSelect.addEventListener('change', () => {
                syncResetLinkState();
                fetchResults(buildUrlFromForm());
            });

            if (resetLink) {
                resetLink.addEventListener('click', (event) => {
                    event.preventDefault();
                    searchInput.value = '';
                    statusSelect.value = '';
                    syncResetLinkState();
                    fetchResults(form.action);
                });
            }

            results.addEventListener('click', (event) => {
                const pageLink = event.target.closest('.pagination-wrap a');

                if (!pageLink) {
                    return;
                }

                event.preventDefault();
                fetchResults(pageLink.href);
            });

            window.addEventListener('popstate', () => {
                fetchResults(window.location.href, { syncForm: true });
            });

            syncResetLinkState();
        });
    </script>
@endpush
