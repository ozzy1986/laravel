<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => is_string($this->title) ? trim($this->title) : $this->title,
        ]);
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status'      => ['required', Rule::enum(TaskStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Название задачи обязательно.',
            'title.max'         => 'Название не должно превышать 255 символов.',
            'description.max'   => 'Описание не должно превышать 5 000 символов.',
            'status.required'   => 'Укажите статус задачи.',
            'status.enum'       => 'Недопустимый статус задачи.',
        ];
    }
}
