<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Models\User;

interface IVotable
{
    /**
     * MorphToMany relationship with User model on 'votables' table
     *
     * @return MorphToMany<User, 'votable'>
     */
    public function votes(): MorphToMany;
}
