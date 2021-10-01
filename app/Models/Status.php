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
