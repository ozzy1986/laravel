@extends('layouts.app')

@section('title', 'Редактирование задачи')

@section('content')
    <p class="section-kicker">Обновление записи</p>
    <h2 class="page-title page-title-tight">Редактирование задачи</h2>
    <p class="page-lead" style="margin-bottom: 1.2rem;">Скорректируйте текст, статус или описание без лишнего визуального шума.</p>

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
