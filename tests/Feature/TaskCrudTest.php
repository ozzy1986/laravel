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

    public function test_update_modifies_task_and_redirects(): void
    {
        $task = Task::factory()->create(['title' => 'Old title']);

        $response = $this->put(route('tasks.update', $task), [
            'title'       => 'New title',
            'description' => 'Updated description',
            'status'      => 'in_progress',
        ]);

        $response->assertRedirect(route('tasks.show', $task));
        $this->assertDatabaseHas('tasks', [
            'id'     => $task->id,
            'title'  => 'New title',
            'status' => 'in_progress',
        ]);
    }

    public function test_update_fails_without_title(): void
    {
        $task = Task::factory()->create();

        $response = $this->put(route('tasks.update', $task), [
            'title'  => '',
            'status' => 'new',
        ]);

        $response->assertSessionHasErrors('title');
    }

    // ── Destroy ─────────────────────────────────────────────

    public function test_destroy_deletes_task_and_redirects(): void
    {
        $task = Task::factory()->create();

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
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
        Task::factory()->count(2)->create();

        $response = $this->get(route('tasks.index', ['status' => 'bogus']));

        $response->assertOk();
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

    // ── Pagination ──────────────────────────────────────────

    public function test_index_paginates_results(): void
    {
        Task::factory()->count(20)->create();

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee('page=2');
    }

    // ── Homepage redirect ───────────────────────────────────

    public function test_homepage_redirects_to_tasks(): void
    {
        $this->get('/')->assertRedirect(route('tasks.index'));
    }
}
