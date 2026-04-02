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

    // ── Excerpt ─────────────────────────────────────────────

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

    // ── Search scope ────────────────────────────────────────

    public function test_scope_search_matches_title(): void
    {
        Task::factory()->create(['title' => 'Buy groceries']);
        Task::factory()->create(['title' => 'Deploy server']);

        $results = Task::query()->search('groceries')->get();

        $this->assertCount(1, $results);
        $this->assertSame('Buy groceries', $results->first()->title);
    }

    public function test_scope_search_does_not_match_description(): void
    {
        Task::factory()->create([
            'title'       => 'Task A',
            'description' => 'unique phrase only in description',
        ]);

        $results = Task::query()->search('unique phrase')->get();

        $this->assertCount(0, $results);
    }

    public function test_scope_search_escapes_percent_wildcard(): void
    {
        Task::factory()->create(['title' => 'Normal task']);
        Task::factory()->create(['title' => '100% complete']);

        $results = Task::query()->search('%')->get();

        $this->assertCount(1, $results);
        $this->assertSame('100% complete', $results->first()->title);
    }

    public function test_scope_search_escapes_underscore_wildcard(): void
    {
        Task::factory()->create(['title' => 'file_name']);
        Task::factory()->create(['title' => 'filename']);

        $results = Task::query()->search('_')->get();

        $this->assertCount(1, $results);
        $this->assertSame('file_name', $results->first()->title);
    }

    public function test_scope_search_escapes_backslash(): void
    {
        Task::factory()->create(['title' => 'path\\to\\file']);
        Task::factory()->create(['title' => 'normal task']);

        $results = Task::query()->search('\\')->get();

        $this->assertCount(1, $results);
        $this->assertSame('path\\to\\file', $results->first()->title);
    }

    public function test_scope_search_ignores_empty_term(): void
    {
        Task::factory()->count(3)->create();

        $results = Task::query()->search('')->get();

        $this->assertCount(3, $results);
    }

    public function test_scope_search_ignores_whitespace_only_term(): void
    {
        Task::factory()->count(2)->create();

        $results = Task::query()->search('   ')->get();

        $this->assertCount(2, $results);
    }

    // ── Filter scope ────────────────────────────────────────

    public function test_scope_filter_status_ignores_invalid_values(): void
    {
        Task::factory()->count(3)->create();

        $results = Task::query()->filterStatus('nonexistent')->get();

        $this->assertCount(3, $results);
    }

    public function test_scope_filter_status_ignores_null(): void
    {
        Task::factory()->count(2)->create();

        $results = Task::query()->filterStatus(null)->get();

        $this->assertCount(2, $results);
    }

    public function test_scope_filter_status_filters_correctly(): void
    {
        Task::factory()->create(['status' => 'new']);
        Task::factory()->create(['status' => 'done']);

        $results = Task::query()->filterStatus('done')->get();

        $this->assertCount(1, $results);
        $this->assertSame('done', $results->first()->status->value);
    }
}
