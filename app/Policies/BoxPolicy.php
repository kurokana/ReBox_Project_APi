<?php

namespace App\Policies;

use App\Models\Box;
use App\Models\User;

class BoxPolicy
{
    /**
     * Determine whether the user can view the box.
     */
    public function view(User $user, Box $box): bool
    {
        return $user->id === $box->user_id;
    }

    /**
     * Determine whether the user can update the box.
     */
    public function update(User $user, Box $box): bool
    {
        return $user->id === $box->user_id;
    }

    /**
     * Determine whether the user can delete the box.
     */
    public function delete(User $user, Box $box): bool
    {
        return $user->id === $box->user_id;
    }
}
