<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;

    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class);
    }

    public function getStylingClasses(): string
    {
        $classes = [
            'open' => 'bg-gray-200',
            'considering' => 'bg-purple text-white',
            'in_progress' => 'bg-yellow text-white',
            'implemented' => 'bg-green text-white',
            'closed' => 'bg-red text-white',
        ];

        return $classes[$this->name];
    }
}
