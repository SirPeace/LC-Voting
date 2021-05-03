<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface IVotable
{
    /**
     * MorphToMany relationship with User model on 'votables' table
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany<App\Models\User, 'votable'>
     */
    public function votes(): MorphToMany;
}
