<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_formatted_id_pads_to_three_digits(): void
    {
        $task = Task::factory()->create();

        $this->assertMatchesRegularExpression('/^#\d{3,}$/', $task->formattedId());
    }

    public function test_excerpt_returns_null_for_empty_description(): void
    {
        $task = Task::factory()->create(['description' => null]);

        $this->assertNull($task->excerpt());
    }

    public function test_excerpt_returns_null_for_whitespace_only_description(): void
    {
        $task = Task::factory()->create(['description' => '   ']);

        $this->assertNull($task->excerpt());
    }

    public function test_excerpt_truncates_long_description(): void
    {
        $task = Task::factory()->create([
            'description' => str_repeat('Слово ', 100),
        ]);

        $excerpt = $task->excerpt(50);

        $this->assertNotNull($excerpt);
        $this->assertLessThanOrEqual(53, mb_strlen($excerpt));
    }

    public function test_excerpt_collapses_whitespace(): void
    {
        $task = Task::factory()->create([
            'description' => "Line one\n\n  Line   two",
        ]);

        $this->assertStringNotContainsString("\n", $task->excerpt());
    }

    public function test_scope_search_escapes_like_wildcards(): void
    {
        Task::factory()->create(['title' => 'Normal task']);
        Task::factory()->create(['title' => '100% complete']);

        $results = Task::query()->search('%')->get();

        $this->assertCount(1, $results);
        $this->assertSame('100% complete', $results->first()->title);
    }

    public function test_scope_filter_status_ignores_invalid_values(): void
    {
        Task::factory()->count(3)->create();

        $results = Task::query()->filterStatus('nonexistent')->get();

        $this->assertCount(3, $results);
    }
}
