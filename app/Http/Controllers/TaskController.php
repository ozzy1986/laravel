<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    private const PER_PAGE = 6;

    public function index(Request $request): View|JsonResponse
    {
        $tasks = Task::query()
            ->filterStatus($request->query('status'))
            ->search($request->query('search'))
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $viewData = [
            'tasks'    => $tasks,
            'statuses' => TaskStatus::cases(),
            'filters'  => $request->only(['status', 'search']),
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('tasks._results', $viewData)->render(),
                'total' => $tasks->total(),
            ]);
        }

        return view('tasks.index', $viewData);
    }

    public function create(): View
    {
        return view('tasks.create', [
            'statuses' => TaskStatus::cases(),
        ]);
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        Task::create($request->validated());

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Задача создана.');
    }

    public function show(Task $task): View
    {
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        return view('tasks.edit', [
            'task'     => $task,
            'statuses' => TaskStatus::cases(),
        ]);
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Задача обновлена.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Задача удалена.');
    }
}
