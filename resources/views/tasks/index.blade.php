@extends('layouts.app')

@section('title', 'Список задач')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:baseline; flex-wrap:wrap; gap:.75rem; margin-bottom:1rem;">
        <h2 class="page-title" style="margin-bottom:0">Задачи</h2>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">+ Создать задачу</a>
    </div>

    <form method="GET" action="{{ route('tasks.index') }}" class="toolbar">
        <input type="text"
               name="search"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="Поиск по названию…">

        <select name="status" onchange="this.form.submit()">
            <option value="">Все статусы</option>
            @foreach($statuses as $s)
                <option value="{{ $s->value }}" @selected(($filters['status'] ?? '') === $s->value)>
                    {{ $s->label() }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">Найти</button>

        @if(!empty($filters['search']) || !empty($filters['status']))
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-sm">Сбросить</a>
        @endif
    </form>

    @forelse($tasks as $task)
        <div class="card">
            <div class="card-title">
                <a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a>
            </div>
            <div class="card-meta">
                <span class="status-chip {{ $task->status->color() }}">{{ $task->status->label() }}</span>
                <span>{{ $task->created_at->translatedFormat('d M Y, H:i') }}</span>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="icon">📋</div>
            <p>Задач пока нет</p>
            <br>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">Создать первую задачу</a>
        </div>
    @endforelse

    @if($tasks->hasPages())
        <div class="pagination-wrap">
            @if($tasks->onFirstPage())
                <span class="disabled">&laquo;</span>
            @else
                <a href="{{ $tasks->previousPageUrl() }}">&laquo;</a>
            @endif

            @foreach($tasks->getUrlRange(1, $tasks->lastPage()) as $page => $url)
                @if($page == $tasks->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($tasks->hasMorePages())
                <a href="{{ $tasks->nextPageUrl() }}">&raquo;</a>
            @else
                <span class="disabled">&raquo;</span>
            @endif
        </div>
    @endif
@endsection
