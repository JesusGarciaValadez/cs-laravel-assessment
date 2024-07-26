<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TodoController extends Controller
{

    public function view(Todo $todo)
    {
        Gate::authorize('view', $todo);
        return $todo;
    }

    public function update(Todo $todo, Request $request)
    {
        // TODO implement the update of the the todo itself to set the completed status to true
    }
}
