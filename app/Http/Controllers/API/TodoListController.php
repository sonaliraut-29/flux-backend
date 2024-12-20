<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TodoList;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = TodoList::where('user_id', $userId);

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $todoList = TodoList::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
        ]);

        return response()->json($todoList, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $todoList = TodoList::with('tasks')->where('user_id', Auth::id())->find($id);
        if (!$todoList) {
            return response()->json(['message' => 'Todo List not found or unauthorized'], 404);
        }
        return response()->json($todoList);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $todoList = TodoList::where('user_id', Auth::id())->find($id);
        if (!$todoList) {
            return response()->json(['message' => 'Todo List not found or unauthorized'], 404);
        }
        $todoList->update($request->all());
        return response()->json($todoList);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $todoList = TodoList::where('user_id', Auth::id())->find($id);
        if (!$todoList) {
            return response()->json(['message' => 'Todo List not found or unauthorized'], 404);
        }
        $todoList->delete();
        return response()->json(['message' => 'Todo List deleted successfully']);
    }
}
