<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Message $message
     * @return mixed
     */
    public function view(User $user, Message $message)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Message $message
     * @return mixed
     */
    public function update(User $user, Message $message)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Message $message
     * @return mixed
     */
    public function delete(User $user, Message $message)
    {
        return $user->id === $message->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Message $message
     * @return mixed
     */
    public function restore(User $user, Message $message)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Message $message
     * @return mixed
     */
    public function forceDelete(User $user, Message $message)
    {
        //
    }
}
