<?php

namespace Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class TodoApiTest extends TestCase
{
    public function testATodoCanOnlyBeViewedByTheOwner()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $userA->id,
            'completed' => false
        ]);

        $response = $this->actingAs($userA)->get(route('api.todo.view', ['todo' => $todo]), );
        $response->assertStatus(200);

        $response = $this->actingAs($userB)->get(route('api.todo.view', ['todo' => $todo]), );
        $response->assertForbidden();
    }

    public function testATodoCanBeMarkedAsCompleted()
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'completed' => false
        ]);

        $response = $this->actingAs($user)->patchJson(route('api.todo.update', ['todo' => $todo]), [
            'completed' => true
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed' => true
        ]);
    }

    public function testOnlyTheOwnerCanUpdateATodoTroughAPolicy()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $userA->id,
            'completed' => false
        ]);

        // As user B I should not be allowed to do these kind of things
        $this->actingAs($userB)->patchJson(route('api.todo.update', ['todo' => $todo]), [
            'completed' => true
        ])->assertForbidden();

        // As user A, I should
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed' => false
        ]);

    }

    // New requirements

    // Send an e-mail to the user the moment he marks a todo as completed
    // From now on users can have many lists that contain todos
    // A list can be shared with other users that are registered in the system

}
