<?php

namespace App\Observers;

use App\Mail\UserChangesNotificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Mail::to($user->email)
            ->queue(new UserChangesNotificationMail());
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        Mail::to('manager@controlfreak.com')
            ->queue(new UserChangesNotificationMail());
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // TODO send the UserDeletedNotificationMail to the user
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
    }

}
