@if($tasks->count() > 0)
    <p class="results-summary">
        Показаны задачи с {{ $tasks->firstItem() }} по {{ $tasks->lastItem() }} из {{ $tasks->total() }}.
    </p>
@endif

@forelse($tasks as $task)
    <article class="card">
        <div class="card-heading">
            <div>
                <h3 class="card-title">
                    <a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a>
                </h3>
                <div class="card-meta">
                    <span class="status-chip {{ $task->status->color() }}">{{ $task->status->label() }}</span>
                    <span>{{ $task->created_at->format('d.m.y') }}</span>
                </div>
            </div>
            <span class="card-stamp">#{{ str_pad((string) $task->id, 3, '0', STR_PAD_LEFT) }}</span>
        </div>

        @if($task->excerpt())
            <blockquote class="task-quote">«{{ $task->excerpt() }}»</blockquote>
        @endif
    </article>
@empty
    <div class="card empty-state">
        <span class="empty-mark">00</span>
        <p>Задач пока нет</p>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">Создать первую задачу</a>
    </div>
@endforelse

@if($tasks->hasPages())
    <p class="pagination-summary">
        Страница {{ $tasks->currentPage() }} из {{ $tasks->lastPage() }}
    </p>

    <nav class="pagination-wrap" aria-label="Навигация по страницам">
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
