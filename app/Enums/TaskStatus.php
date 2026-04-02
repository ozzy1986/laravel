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

    /** CSS class for the left stripe gradient on list cards */
    public function stripeClass(): string
    {
        return match ($this) {
            self::New        => 'card--stripe-new',
            self::InProgress => 'card--stripe-progress',
            self::Done       => 'card--stripe-done',
        };
    }
}
