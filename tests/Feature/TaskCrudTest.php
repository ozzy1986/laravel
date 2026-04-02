<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCrudTest extends TestCase
{
    use RefreshDatabase;

    // ── Index ───────────────────────────────────────────────

    public function test_index_displays_tasks(): void
    {
        $tasks = Task::factory()->count(3)->create();

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        foreach ($tasks as $task) {
            $response->assertSee(e($task->title));
        }
    }

    public function test_index_shows_empty_state_when_no_tasks(): void
    {
        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee('Задач пока нет');
    }

    public function test_index_shows_description_excerpt_when_available(): void
    {
        Task::factory()->create([
            'title'       => 'Quoted task',
            'description' => 'Это достаточно длинное описание, чтобы убедиться, что в списке задач появляется короткая цитата из текста.',
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee('короткая цитата');
    }

    // ── Show ────────────────────────────────────────────────

    public function test_show_displays_single_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->get(route('tasks.show', $task));

        $response->assertOk();
        $response->assertSee(e($task->title));
        $response->assertSee(e($task->description));
    }

    public function test_show_returns_404_for_missing_task(): void
    {
        $this->get(route('tasks.show', 999))->assertNotFound();
    }

    // ── Create / Store ──────────────────────────────────────

    public function test_create_form_is_accessible(): void
    {
        $this->get(route('tasks.create'))->assertOk();
    }

    public function test_store_creates_task_and_redirects(): void
    {
        $data = [
            'title'       => 'Test task title',
            'description' => 'Some description',
            'status'      => 'new',
        ];

        $response = $this->post(route('tasks.store'), $data);

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'Задача создана.');
        $this->assertDatabaseHas('tasks', ['title' => 'Test task title']);
    }

    public function test_store_fails_without_title(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title'  => '',
            'status' => 'new',
        ]);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_store_fails_with_invalid_status(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title'  => 'Valid title',
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_store_rejects_title_exceeding_max_length(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title'  => str_repeat('a', 256),
            'status' => 'new',
        ]);

        $response->assertSessionHasErrors('title');
    }

    // ── Edit / Update ───────────────────────────────────────

    public function test_edit_form_is_accessible(): void
    {
        $task = Task::factory()->create();

        $this->get(route('tasks.edit', $task))->assertOk();
    }

    public function test_edit_returns_404_for_missing_task(): void
    {
        $this->get(route('tasks.edit', 999))->assertNotFound();
    }

    public function test_update_modifies_task_and_redirects(): void
    {
        $task = Task::factory()->create(['title' => 'Old title']);

        $response = $this->put(route('tasks.update', $task), [
            'title'       => 'New title',
            'description' => 'Updated description',
            'status'      => 'in_progress',
        ]);

        $response->assertRedirect(route('tasks.show', $task));
        $response->assertSessionHas('success', 'Задача обновлена.');
        $this->assertDatabaseHas('tasks', [
            'id'     => $task->id,
            'title'  => 'New title',
            'status' => 'in_progress',
        ]);
    }

    public function test_update_fails_without_title(): void
    {
        $task = Task::factory()->create(['title' => 'Original']);

        $response = $this->put(route('tasks.update', $task), [
            'title'  => '',
            'status' => 'new',
        ]);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Original']);
    }

    public function test_update_fails_with_invalid_status(): void
    {
        $task = Task::factory()->create(['status' => 'new']);

        $response = $this->put(route('tasks.update', $task), [
            'title'  => $task->title,
            'status' => 'bogus',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'new']);
    }

    public function test_update_returns_404_for_missing_task(): void
    {
        $this->put(route('tasks.update', 999), [
            'title'  => 'Title',
            'status' => 'new',
        ])->assertNotFound();
    }

    // ── Destroy ─────────────────────────────────────────────

    public function test_destroy_deletes_task_and_redirects(): void
    {
        $task = Task::factory()->create();

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'Задача удалена.');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_destroy_returns_404_for_missing_task(): void
    {
        $this->delete(route('tasks.destroy', 999))->assertNotFound();
    }

    // ── Status change ───────────────────────────────────────

    public function test_status_can_be_changed_via_update(): void
    {
        $task = Task::factory()->create(['status' => 'new']);

        $this->put(route('tasks.update', $task), [
            'title'  => $task->title,
            'status' => 'done',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id'     => $task->id,
            'status' => 'done',
        ]);
    }

    // ── Filter by status ────────────────────────────────────

    public function test_index_filters_by_status(): void
    {
        Task::factory()->create(['title' => 'Task A', 'status' => 'new']);
        Task::factory()->create(['title' => 'Task B', 'status' => 'in_progress']);
        Task::factory()->create(['title' => 'Task C', 'status' => 'done']);

        $response = $this->get(route('tasks.index', ['status' => 'in_progress']));

        $response->assertOk();
        $response->assertSee('Task B');
        $response->assertDontSee('Task A');
        $response->assertDontSee('Task C');
    }

    public function test_index_ignores_invalid_filter_status(): void
    {
        Task::factory()->create(['title' => 'Visible A']);
        Task::factory()->create(['title' => 'Visible B']);

        $response = $this->get(route('tasks.index', ['status' => 'bogus']));

        $response->assertOk();
        $response->assertSee('Visible A');
        $response->assertSee('Visible B');
    }

    // ── Search by title ─────────────────────────────────────

    public function test_index_searches_by_title(): void
    {
        Task::factory()->create(['title' => 'Buy groceries']);
        Task::factory()->create(['title' => 'Deploy server']);

        $response = $this->get(route('tasks.index', ['search' => 'groceries']));

        $response->assertOk();
        $response->assertSee('Buy groceries');
        $response->assertDontSee('Deploy server');
    }

    public function test_index_search_does_not_match_description(): void
    {
        Task::factory()->create([
            'title'       => 'Alpha',
            'description' => 'Уникальная фраза для поиска по описанию',
        ]);
        Task::factory()->create(['title' => 'Beta']);

        $response = $this->get(route('tasks.index', ['search' => 'Уникальная фраза']));

        $response->assertOk();
        $response->assertDontSee('Alpha');
    }

    // ── AJAX index ──────────────────────────────────────────

    public function test_ajax_index_returns_partial_html_for_search(): void
    {
        Task::factory()->create(['title' => 'Buy groceries']);
        Task::factory()->create(['title' => 'Deploy server']);

        $response = $this->get(route('tasks.index', ['search' => 'groceries']), [
            'Accept'           => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['html', 'total']);
        $this->assertStringContainsString('Buy groceries', $response->json('html'));
        $this->assertStringNotContainsString('Deploy server', $response->json('html'));
    }

    public function test_ajax_index_filters_by_status(): void
    {
        Task::factory()->create(['title' => 'Task A', 'status' => 'new']);
        Task::factory()->create(['title' => 'Task B', 'status' => 'done']);

        $response = $this->get(route('tasks.index', ['status' => 'done']), [
            'Accept'           => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $this->assertStringContainsString('Task B', $response->json('html'));
        $this->assertStringNotContainsString('Task A', $response->json('html'));
        $this->assertSame(1, $response->json('total'));
    }

    public function test_ajax_index_combines_search_and_status(): void
    {
        Task::factory()->create(['title' => 'Buy milk', 'status' => 'new']);
        Task::factory()->create(['title' => 'Buy bread', 'status' => 'done']);
        Task::factory()->create(['title' => 'Fix bug', 'status' => 'new']);

        $response = $this->get(route('tasks.index', ['search' => 'Buy', 'status' => 'new']), [
            'Accept'           => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $this->assertStringContainsString('Buy milk', $response->json('html'));
        $this->assertStringNotContainsString('Buy bread', $response->json('html'));
        $this->assertStringNotContainsString('Fix bug', $response->json('html'));
        $this->assertSame(1, $response->json('total'));
    }

    // ── Pagination ──────────────────────────────────────────

    public function test_index_paginates_results_by_six_items_per_page(): void
    {
        foreach (range(1, 7) as $number) {
            Task::factory()->create([
                'title'      => "Task {$number}",
                'created_at' => now()->subMinutes(8 - $number),
                'updated_at' => now()->subMinutes(8 - $number),
            ]);
        }

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee('Task 7');
        $response->assertDontSee('Task 1');
        $response->assertSee('page=2');
    }

    public function test_pagination_preserves_query_string(): void
    {
        foreach (range(1, 7) as $number) {
            Task::factory()->create([
                'title'      => "Filter {$number}",
                'status'     => 'new',
                'created_at' => now()->subMinutes(8 - $number),
                'updated_at' => now()->subMinutes(8 - $number),
            ]);
        }

        $response = $this->get(route('tasks.index', ['status' => 'new']));

        $response->assertOk();
        $response->assertSee('status=new');
        $response->assertSee('page=2');
    }

    // ── Combined filters ────────────────────────────────────

    public function test_index_combines_status_filter_and_search(): void
    {
        Task::factory()->create(['title' => 'Buy milk', 'status' => 'new']);
        Task::factory()->create(['title' => 'Buy bread', 'status' => 'done']);
        Task::factory()->create(['title' => 'Fix bug', 'status' => 'new']);

        $response = $this->get(route('tasks.index', [
            'status' => 'new',
            'search' => 'Buy',
        ]));

        $response->assertOk();
        $response->assertSee('Buy milk');
        $response->assertDontSee('Buy bread');
        $response->assertDontSee('Fix bug');
    }

    // ── Validation edge cases ───────────────────────────────

    public function test_store_trims_title_whitespace(): void
    {
        $this->post(route('tasks.store'), [
            'title'  => '  Clean title  ',
            'status' => 'new',
        ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Clean title']);
    }

    public function test_store_normalizes_whitespace_only_description_to_null(): void
    {
        $this->post(route('tasks.store'), [
            'title'       => 'Title',
            'description' => '   ',
            'status'      => 'new',
        ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Title', 'description' => null]);
    }

    public function test_store_fails_with_whitespace_only_title(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title'  => '   ',
            'status' => 'new',
        ]);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_store_rejects_description_exceeding_max_length(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title'       => 'Valid title',
            'description' => str_repeat('a', 5001),
            'status'      => 'new',
        ]);

        $response->assertSessionHasErrors('description');
    }

    // ── XSS safety ──────────────────────────────────────────

    public function test_xss_payload_in_title_is_escaped_in_listing(): void
    {
        Task::factory()->create([
            'title' => '<script>alert("xss")</script>',
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
    }

    public function test_xss_payload_in_title_is_escaped_on_show(): void
    {
        $task = Task::factory()->create([
            'title' => '<script>alert("xss")</script>',
        ]);

        $response = $this->get(route('tasks.show', $task));

        $response->assertOk();
        $response->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
    }

    public function test_xss_payload_in_description_is_escaped_on_show(): void
    {
        $task = Task::factory()->create([
            'description' => '<img src=x onerror=alert(1)>',
        ]);

        $response = $this->get(route('tasks.show', $task));

        $response->assertOk();
        $response->assertSee('&lt;img src=x onerror=alert(1)&gt;', false);
    }

    // ── Homepage redirect ───────────────────────────────────

    public function test_homepage_redirects_to_tasks(): void
    {
        $this->get('/')->assertRedirect(route('tasks.index'));
    }
}
