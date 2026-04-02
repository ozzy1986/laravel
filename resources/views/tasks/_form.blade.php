<div class="form-group">
    <label for="title">Название</label>
    <input type="text"
           id="title"
           name="title"
           value="{{ old('title', $task->title ?? '') }}"
           required
           maxlength="255"
           @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>
    @error('title')
        <div class="form-error" id="title-error" role="alert">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="description">Описание</label>
    <textarea id="description"
              name="description"
              maxlength="5000"
              @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>{{ old('description', $task->description ?? '') }}</textarea>
    @error('description')
        <div class="form-error" id="description-error" role="alert">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="status">Статус</label>
    <select id="status"
            name="status"
            required
            @error('status') aria-invalid="true" aria-describedby="status-error" @enderror>
        @foreach($statuses as $s)
            <option value="{{ $s->value }}"
                @selected(old('status', $task->status->value ?? 'new') === $s->value)>
                {{ $s->label() }}
            </option>
        @endforeach
    </select>
    @error('status')
        <div class="form-error" id="status-error" role="alert">{{ $message }}</div>
    @enderror
</div>
