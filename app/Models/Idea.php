<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Idea extends Model
{
    use HasFactory, Sluggable;

    const PAGINATION_COUNT = 10;

    protected $guarded = [];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'votes');
    }

    public function isVotedByUser(?User $user): bool
    {
        // In case user is not logged in
        if (!$user) {
            return false;
        }

        return Vote::where('idea_id', $this->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function voteByUser(?User $user): bool
    {
        // In case user is not logged in
        if (!$user) {
            return false;
        }

        Vote::factory()->createOne([
            'idea_id' => $this->id,
            'user_id' => $user->id,
        ]);

        return true;
    }

    public function unvoteByUser(?User $user): bool
    {
        // In case user is not logged in
        if (!$user) {
            return false;
        }

        Vote::where('idea_id', $this->id)
            ->where('user_id', $user->id)
            ->delete();

        return true;
    }
}
