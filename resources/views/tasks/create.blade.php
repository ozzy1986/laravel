@extends('layouts.app')

@section('title', 'Новая задача')

@section('content')
    <p class="section-kicker">Новая запись</p>
    <h2 class="page-title page-title-tight">Новая задача</h2>
    <p class="page-lead" style="margin-bottom: 1.2rem;">Короткое название, понятный статус и при желании пара строк контекста.</p>

    <div class="card">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            @include('tasks._form')
            <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                <button type="submit" class="btn btn-primary">Создать</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
@endsection
