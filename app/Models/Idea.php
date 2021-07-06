<?php

namespace App\Models;

use App\Interfaces\IVotable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Idea extends Model implements IVotable
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

    public function votes(): MorphToMany
    {
        return $this->morphToMany(User::class, 'votable');
    }

    public function spamMarks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'idea_spam_marks');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function markAsSpam(User $user): void
    {
        try {
            $this->spamMarks()->save($user);
        } catch (\Exception $e) {
            // User already marked idea as spam
        }
    }

    public function removeSpamMarks(): void
    {
        $this->spamMarks()->detach();
    }
}
