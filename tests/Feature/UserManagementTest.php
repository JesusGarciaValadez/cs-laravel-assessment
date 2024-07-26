<?php

namespace Feature;

use App\Mail\UserChangesNotificationMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    const ALLOWED_PASSWORD = 'xu2^3djdL@3';

    #[Test]
    public function itStoresTheUsername()
    {
        /* We've decided to add usernames to our users. However, things are not fully working yet. */

        $result = $this->postJson(route('api.user-registration.register'), [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'username' => 'john',
            'password' => 'xu2^3djd@3'
        ]);

        $result->assertCreated();
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'username' => 'john'
        ]);
    }

    #[Test]
    public function itOnlyAllowsUniqueUsernames()
    {
        /* We've decided to add usernames to our users. However, things are not fully working yet. */

        // The first john is allowed
        $this->postJson(route('api.user-registration.register'), [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'username' => 'john',
            'password' => self::ALLOWED_PASSWORD,
            'password_confirmation' => self::ALLOWED_PASSWORD
        ])->assertCreated();
        $this->assertDatabaseHas('users', [
           'username' => 'john'
        ]);
        $this->assertDatabaseCount('users', 1);

        // Now another john comes along
        $this->postJson(route('api.user-registration.register'), [
            'name' => 'John Wayne',
            'email' => 'john@wayne.com',
            'username' => 'john',
            'password' => self::ALLOWED_PASSWORD,
            'password_confirmation' => self::ALLOWED_PASSWORD
        ])->assertUnprocessable();

        $this->assertDatabaseCount('users', 1);
    }

    #[Test]
    public function itDoesNotExposeThePassword(){
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('api.users.me'))
            ->assertJsonMissingPath('password');
    }

    #[Test]
    #[DataProvider('strongPasswordDataProvider')]
    public function itEnsuresPeopleUseStrongPasswords(string $password, bool $expectedValid)
    {
        // Passwords should be minimal 10 characters and:, 1 cap, 1 symbol,
        // 1 lowercase character
        // 1 uppercase character
        // 1 digit
        // 1 symbol

        $response = $this->postJson(route('api.user-registration.register'), [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'username' => 'john',
            'password' => $password,
            'password_confirmation' => $password
        ]);

        if ($expectedValid) {
            $response->assertCreated();
        } else {
            $response->assertStatus(422); // Unprocessable Entity
        }
    }

    public static function strongPasswordDataProvider(): array
    {
        return [
            'too short' => ['1h@X4lf', false],
            'missing uppercase' => ['xu2^3djd@3', false],
            'missing lowercase' => ['XU2^3DLD@3', false],
            'missing digit' => ['xu^jdL@abc', false],
            'missing symbol' => ['xu23djdLks', false],
            'valid password' => ['xu2^3djdL@3', true],
        ];
    }

    #[Test]
    #[DataProvider('usernameDataProvider')]
    public function itDoesNotAllowUsersWithTheSameNameAsOurManager($username, $expected)
    {
        /* Our manager of our company is kind of a control freak.
            He does not want any user to contain his first name.
            Someone in our team took the effort to validate the input on the user registration form.
            However, things are not working as expected.
        */

        $result = $this->postJson(route('api.user-registration.register'), [
            'name' => 'Brian Doe',
            'email' => 'brian@doe.com',
            'username' => $username,
            'password' => 'xu2^3djd@3'
        ]);

        if ($expected) {
            $result->assertCreated();
            $this->assertDatabaseHas('users', [
                'username' => $username
            ]);
        } else {
            $result->assertStatus(422);
            $this->assertDatabaseEmpty('users');
        }
    }

    public static function usernameDataProvider(): array
    {
        return [
            ['Brian', false],  // Manager's name should be invalid
            ['brian', false],  // Case insensitive check
            ['Fabriane', false], // Should be valid
            ['Alice', true],  // Should be valid
        ];
    }

    #[Test]
    public function itSendsAnEmailWhenAUserGetsDeleted()
    {
        /* As mentioned, the manager of our company is kind of a control freak.
            He wants to be updated every time something changes on a user */

        $user = User::factory()->create([
            'name' => 'Wrong name'
        ]);

        Mail::fake();

        $user->delete();
        // So, when a user gets updated there should be an e-mail queued
        Mail::assertOutgoingCount(1);
    }

    #[Test]
    public function itQueuesAnEmailEveryTimeAUserGetsUpdated()
    {
        Mail::fake();

        /* As mentioned, the manager of our company is kind of a control freak.
            He wants to be updated every time something changes on a user */

        $user = User::factory()->create([
            'name' => 'Wrong name'
        ]);

        $user->update([
            'name' => 'Correct name'
        ]);
        // So, when a user gets updated there should be an e-mail queued
        Mail::assertQueued(UserChangesNotificationMail::class, function ($mail) use ($user) {
            return $mail->to[0]['address'] == 'manager@controlfreak.com';
        });
        Mail::assertOutgoingCount(1);
    }
}
