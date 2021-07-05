<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name, 2)[0];
    }

    public function getLastNameAttribute(): string
    {
        return explode(' ', $this->name, 2)[1];
    }

    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class);
    }

    public function ideaVotes(): BelongsToMany
    {
        return $this->morphedByMany(Idea::class, 'votable');
    }

    public function getAvatar(): string
    {
        $firstCharacter = $this->email[0];

        $integerToUse = is_numeric($firstCharacter)
            ? ord(strtolower($firstCharacter)) - 21
            : ord(strtolower($firstCharacter)) - 96;

        return 'https://www.gravatar.com/avatar/'
            . md5($this->email)
            . '?s=200'
            . '&d=https://s3.amazonaws.com/laracasts/images/forum/avatars/default-avatar-'
            . $integerToUse
            . '.png';
    }

    public function isAdmin(): bool
    {
        return in_array($this->email, [
            'roman.khabibulin12@gmail.com',
        ]);
    }

    public function isAdmin()
    {
        return in_array($this->email, [
            'jeffrey@laracasts.com',
            'andre_madarang@hotmail.com',
            'adrian@laracasts.com,'
        ]);
    }
}
