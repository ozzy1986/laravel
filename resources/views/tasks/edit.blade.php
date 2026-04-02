@extends('layouts.app')

@section('title', 'Редактирование задачи')

@section('content')
    <h2 class="page-title page-title-tight">Редактирование</h2>

    <div class="card">
        <form method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf
            @method('PUT')
            @include('tasks._form')
            <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
@endsection
