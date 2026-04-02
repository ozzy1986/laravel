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
            <span class="card-stamp">#{{ str_pad((string) $task->id, 3, '0', STR_PAD_LEFT) }}</span>
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
    <p class="pagination-summary">
        {{ $tasks->currentPage() }} / {{ $tasks->lastPage() }}
    </p>

    <nav class="pagination-wrap" aria-label="Страницы">
        @if($tasks->onFirstPage())
            <span class="disabled">&laquo;</span>
        @else
            <a href="{{ $tasks->previousPageUrl() }}" rel="prev">&laquo;</a>
        @endif

        @foreach($tasks->getUrlRange(1, $tasks->lastPage()) as $page => $url)
            @if($page === $tasks->currentPage())
                <span class="active">{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach

        @if($tasks->hasMorePages())
            <a href="{{ $tasks->nextPageUrl() }}" rel="next">&raquo;</a>
        @else
            <span class="disabled">&raquo;</span>
        @endif
    </nav>
@endif
