<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;

abstract class UserManagementTest extends BaseTestCase
{
    #[Test]
    public function itQueuesAnEmailEveryTimeAUserGetsUpdated()
    {
        Queue::fake();

        /* The manager of our company is kind of a control freak.
            He wants to be updated every time something changes on a user */

        $user = User::factory()->create([
            'name' => 'Wrong name'
        ]);

        $user->update([
            'name' => 'Correct name'
        ]);
        // So, when a user gets updated there should be an e-mail queued
        Queue::assertCount(1);
    }
}
