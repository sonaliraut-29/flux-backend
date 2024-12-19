<?php
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TodoListController;
use App\Http\Controllers\API\TaskController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
Route::group(['middleware' => 'auth:api'], function(){
    Route::post('details', [UserController::class, 'details']);
    Route::get('logout', [UserController::class, 'logout']);

    Route::prefix('todo-lists')->group(function () {
        Route::get('/', [TodoListController::class, 'index']);
        Route::post('/', [TodoListController::class, 'store']);
        Route::get('{id}', [TodoListController::class, 'show']);
        Route::put('{id}', [TodoListController::class, 'update']);
        Route::delete('{id}', [TodoListController::class, 'destroy']); 
        
        // Routes for tasks within a specific todo list
        Route::prefix('{todo_list_id}/tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index']);
            Route::post('/', [TaskController::class, 'store']);
            Route::put('{id}/mark-complete', [TaskController::class, 'complete']);
            Route::get('{id}', [TaskController::class, 'show']);
            Route::put('{id}', [TaskController::class, 'update']);
            Route::delete('{id}', [TaskController::class, 'destroy']);
        });
    });
});
