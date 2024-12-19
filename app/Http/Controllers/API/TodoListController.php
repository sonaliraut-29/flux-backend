<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TodoList;
use Illuminate\Http\Request;

class TodoListController extends Controller
{
    public function index(Request $request)
    {
        $query = TodoList::query();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('sortBy') && $request->sortBy) {
            $sortBy = $request->sortBy;
            $sortDirection = $request->has('orderby') ? $request->orderby : 'asc';
            if (in_array($sortBy, ['name', 'created_at'])) {
                $query->orderBy($sortBy, $sortDirection);
            }
        }
        $todoLists = $query->get();

        return response()->json($todoLists);
    }


    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $todoList = TodoList::create($request->all());
        return response()->json($todoList, 201);
    }

    public function show($id)
    {
        $todoList = TodoList::with('tasks')->find($id);
        if (!$todoList) {
            return response()->json(['message' => 'Todo List not found'], 404);
        }
        return response()->json($todoList);
    }

    public function update(Request $request, $id)
    {
        $todoList = TodoList::find($id);
        if (!$todoList) {
            return response()->json(['message' => 'Todo List not found'], 404);
        }
        $todoList->update($request->all());
        return response()->json($todoList);
    }

    public function destroy($id)
    {
        $todoList = TodoList::find($id);
        if (!$todoList) {
            return response()->json(['message' => 'Todo List not found'], 404);
        }
        $todoList->delete();
        return response()->json(['message' => 'Todo List deleted successfully']);
    }
}
