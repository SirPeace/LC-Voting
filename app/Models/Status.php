<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

    public static function getStatusesCount(): array
    {
        return Idea::query()
            ->selectRaw('COUNT(*) AS "all"')
            ->selectRaw('COUNT(CASE WHEN status_id = 1 THEN 1 END) AS "open"')
            ->selectRaw('COUNT(CASE WHEN status_id = 2 THEN 1 END) AS "considering"')
            ->selectRaw('COUNT(CASE WHEN status_id = 3 THEN 1 END) AS "in_progress"')
            ->selectRaw('COUNT(CASE WHEN status_id = 4 THEN 1 END) AS "implemented"')
            ->selectRaw('COUNT(CASE WHEN status_id = 5 THEN 1 END) AS "closed"')
            ->first()
            ->toArray();
    }
}
