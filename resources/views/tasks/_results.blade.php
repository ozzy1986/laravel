@if($tasks->count() > 0)
    <p class="results-summary">
        {{ $tasks->firstItem() }}–{{ $tasks->lastItem() }} из {{ $tasks->total() }}
    </p>
@endif

@forelse($tasks as $task)
    <a href="{{ route('tasks.show', $task) }}" class="card task-card {{ $task->status->stripeClass() }}">
        <div class="card-heading">
            <div>
                <h3 class="card-title">{{ $task->title }}</h3>
                <div class="card-meta">
                    <span class="status-chip {{ $task->status->color() }}">{{ $task->status->label() }}</span>
                    <time datetime="{{ $task->created_at->toIso8601String() }}">{{ $task->created_at->format('d.m.y H:i') }}</time>
                </div>
            </div>
            <span class="card-stamp">{{ $task->formattedId() }}</span>
        </div>

        @if($task->excerpt())
            <blockquote class="task-quote">«{{ $task->excerpt() }}»</blockquote>
        @endif
    </a>
@empty
    <div class="card empty-state">
        <span class="empty-mark">00</span>
        <p>Задач пока нет</p>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">Создать первую задачу</a>
    </div>
@endforelse

@if($tasks->hasPages())
    @php
        $current  = $tasks->currentPage();
        $last     = $tasks->lastPage();
        $side     = 2;
        $urls     = $tasks->getUrlRange(1, $last);

        $from = max(2, $current - $side);
        $to   = min($last - 1, $current + $side);

        $pages = collect();
        $pages->push(1);

        if ($from > 2) {
            $pages->push(null);
        }

        for ($i = $from; $i <= $to; $i++) {
            $pages->push($i);
        }

        if ($to < $last - 1) {
            $pages->push(null);
        }

        if ($last > 1) {
            $pages->push($last);
        }
    @endphp

    <p class="pagination-summary">
        {{ $current }} / {{ $last }}
    </p>

    <nav class="pagination-wrap" aria-label="Страницы">
        @if($tasks->onFirstPage())
            <span class="disabled" aria-disabled="true" aria-label="Предыдущая страница">&laquo;</span>
        @else
            <a href="{{ $tasks->previousPageUrl() }}" rel="prev" aria-label="Предыдущая страница">&laquo;</a>
        @endif

        @foreach($pages as $page)
            @if(is_null($page))
                <span class="pagination-ellipsis" aria-hidden="true">&hellip;</span>
            @elseif($page === $current)
                <span class="active" aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $urls[$page] }}">{{ $page }}</a>
            @endif
        @endforeach

        @if($tasks->hasMorePages())
            <a href="{{ $tasks->nextPageUrl() }}" rel="next" aria-label="Следующая страница">&raquo;</a>
        @else
            <span class="disabled" aria-disabled="true" aria-label="Следующая страница">&raquo;</span>
        @endif
    </nav>
@endif
