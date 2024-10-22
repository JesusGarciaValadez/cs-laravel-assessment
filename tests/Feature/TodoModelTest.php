<?php

namespace Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class TodoModelTest extends TestCase
{
    public function testThatATodoCanNeverBeCreatedWithoutAnExistingUser()
    {
        $this->expectExceptionCode(23000);
        Todo::factory()->create([
            'user_id' => 3452
        ]);
    }

    public function testTodosCascadeOnDelete()
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $user->id
        ]);

        $user->delete();
        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id
        ]);
    }

}
