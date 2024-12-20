<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /*
     * Display a listing of the resource.
    */
    public function index(Request $request, $todo_list_id)
    {
        $todoList = TodoList::find($todo_list_id);
        if (!$todoList) {
            return response()->json(['message' => 'Todo List not found'], 404);
        }

        $query = $todoList->tasks();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->has('isCompleted')) {
            $completedStatus = $request->input('isCompleted');
            $query->where('status', $completedStatus);
        }

        if ($request->has('sort_by') && $request->has('orderby')) {
            $sortBy = $request->input('sort_by');
            $order = $request->input('orderby');
            if (in_array($sortBy, ['title', 'created_at']) && in_array(strtolower($order), ['asc', 'desc'])) {
                $query->orderBy($sortBy, $order);
            }
        }
        $tasks = $query->get();
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $todo_list_id)
    {
        $todoList = TodoList::find($todo_list_id);
        if (!$todoList) {
            return response()->json(['message' => 'Todo List not found'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            // 'description' => 'required|string',
        ]);

        $task = $todoList->tasks()->create($request->all());
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($todo_list_id, $id)
    {
        $task = Task::where('todo_list_id', $todo_list_id)->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $todo_list_id, $id)
    {
        $task = Task::where('todo_list_id', $todo_list_id)->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->update($request->all());
        return response()->json($task);
    }
    /**
     *  Remove the specified resource from storage.
     */
    public function destroy($todo_list_id, $id)
    {
        $task = Task::where('todo_list_id', $todo_list_id)->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }

    /**
     * Complete the specified resource from storage.
     */
    public function complete($todo_list_id, $id) {
        $task = Task::where('todo_list_id', $todo_list_id)->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->status = !$task->status;
        $task->save();
        return response()->json($task);
    }
}
