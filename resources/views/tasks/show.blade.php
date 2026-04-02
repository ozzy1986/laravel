@extends('layouts.app')

@section('title', $task->title)

@section('content')
    <div class="task-detail">
        <h2 class="page-title">{{ $task->title }}</h2>

        <div class="meta-bar">
            <span class="status-chip {{ $task->status->color() }}">{{ $task->status->label() }}</span>
            <span>Создана: {{ $task->created_at->translatedFormat('d M Y, H:i') }}</span>
            @if($task->updated_at->gt($task->created_at))
                <span>Обновлена: {{ $task->updated_at->translatedFormat('d M Y, H:i') }}</span>
            @endif
        </div>

        @if($task->description)
            <div class="description">{{ $task->description }}</div>
        @endif

        <div class="task-actions">
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-secondary">Редактировать</a>
            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Удалить задачу?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">К списку</a>
        </div>
    </div>
@endsection
