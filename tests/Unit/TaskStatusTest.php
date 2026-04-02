<?php

namespace Tests\Unit;

use App\Enums\TaskStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TaskStatusTest extends TestCase
{
    public static function statusDataProvider(): array
    {
        return [
            'new'         => [TaskStatus::New, 'new', 'Новая', 'status-new', 'card--stripe-new'],
            'in_progress' => [TaskStatus::InProgress, 'in_progress', 'В работе', 'status-progress', 'card--stripe-progress'],
            'done'        => [TaskStatus::Done, 'done', 'Выполнена', 'status-done', 'card--stripe-done'],
        ];
    }

    #[DataProvider('statusDataProvider')]
    public function test_each_status_returns_correct_attributes(
        TaskStatus $status,
        string $value,
        string $label,
        string $color,
        string $stripe,
    ): void {
        $this->assertSame($value, $status->value);
        $this->assertSame($label, $status->label());
        $this->assertSame($color, $status->color());
        $this->assertSame($stripe, $status->stripeClass());
    }

    public function test_all_cases_are_covered(): void
    {
        $this->assertCount(3, TaskStatus::cases());
    }
}
