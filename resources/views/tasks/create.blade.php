@extends('layouts.app')

@section('title', 'Новая задача')

@section('content')
    <h2 class="page-title page-title-tight">Новая задача</h2>

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
