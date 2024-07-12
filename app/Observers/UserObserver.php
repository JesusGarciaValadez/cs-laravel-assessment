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
        $this->notifyManager($user, 'created');
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->notifyManager($user, 'updated');
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->notifyManager($user, 'deleted');
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $this->notifyManager($user, 'restored');
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        $this->notifyManager($user, 'force-deleted');
    }

    private function notifyManager(User $user, string $string)
    {
        $message = (new UserChangesNotificationMail());

        Mail::to('controlfreak@manager.com')
            ->queue($message);
    }
}
