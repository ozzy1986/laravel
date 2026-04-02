<?php

namespace App\Enums;

enum TaskStatus: string
{
    case New        = 'new';
    case InProgress = 'in_progress';
    case Done       = 'done';

    public function label(): string
    {
        return match ($this) {
            self::New        => 'Новая',
            self::InProgress => 'В работе',
            self::Done       => 'Выполнена',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New        => 'status-new',
            self::InProgress => 'status-progress',
            self::Done       => 'status-done',
        };
    }
}
