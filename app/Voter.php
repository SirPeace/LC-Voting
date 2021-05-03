<?php

namespace App;

use App\Interfaces\IVotable;
use App\Models\User;
use App\Models\Votable;

class Voter
{
    public function __construct(
        protected IVotable $votable
    ) {
    }

    /**
     * Check if the specified user voted for this votable entity
     *
     * @param App\Models\User|null $user
     * @return boolean
     */
    public function isVotedBy(?User $user): bool
    {
        // Check whether the user is logged in
        if (!$user) {
            return false;
        }

        return Votable::where('votable_id', $this->votable->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Vote for this votable entity as the specified user
     *
     * @param App\Models\User|null $user
     * @return void
     */
    public function vote(?User $user): void
    {
        if ($this->isVotedBy($user)) return;

        $this->votable->votes()->save($user);
    }

    /**
     * Unvote this votable entity as the specified user
     *
     * @param App\Models\User|null $user
     * @return void
     */
    public function unvote(?User $user): void
    {
        if (!$this->isVotedBy($user)) return;

        $this->votable->votes()->detach($user);
    }
}
