@extends('layouts.app')

@section('title', $task->title)

@section('content')
    <div class="task-detail">
        <h2 class="page-title page-title-tight">{{ $task->title }}</h2>

        <div class="card">
            <div class="meta-bar">
                <span class="status-chip {{ $task->status->color() }}">{{ $task->status->label() }}</span>
                <span>Создана: {{ $task->created_at->format('d.m.y H:i') }}</span>
                @if($task->updated_at->gt($task->created_at))
                    <span>Обновлена: {{ $task->updated_at->format('d.m.y H:i') }}</span>
                @endif
            </div>

            @if($task->description)
                <div class="description">{{ $task->description }}</div>
            @else
                <p class="description-empty">Описание пока не добавлено.</p>
            @endif

            <div class="task-actions">
                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-secondary">Редактировать</a>
                <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" data-confirm="Удалить задачу «{{ $task->title }}»?">Удалить</button>
                </form>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">К списку</a>
            </div>
        </div>
    </div>
@endsection
