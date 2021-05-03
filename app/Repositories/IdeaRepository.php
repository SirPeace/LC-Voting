<?php

namespace App\Repositories;

use App\Models\Idea;
use App\Models\Status;
use App\Models\Votable;
use App\Models\Category;
use Illuminate\Pagination\Paginator;

class IdeaRepository
{
    /**
     * Get paginated list of Idea models to render on index page
     *
     * @param string $category Category name query string
     * @param string $filter Filter option query string
     * @param string $search Search value query string
     * @param string $status Status name query string
     * @param string $orderBy Show latest posts according to this field
     * @return Illuminate\Pagination\Paginator
     */
    static public function getIdeasForIndex(
        string $category = '',
        string $filter = '',
        string $search = '',
        string $status = '',
        string $orderBy = 'id'
    ): Paginator {
        $categories = Category::all()->toBase();

        $ideasPaginator = Idea::with('user', 'category', 'status') // eager-load relationships (n+1)
            ->when(
                $status,
                function ($query) use ($status) {
                    $statuses = Status::pluck('id', 'name');
                    return $query->where('status_id', $statuses[$status]);
                }
            )
            ->when(
                $category,
                function ($query) use ($categories, $category) {
                    $categories = $categories->pluck('id', 'name');
                    return $query->where('category_id', $categories[$category]);
                }
            )
            ->when(
                $filter,
                function ($query) use ($filter) {
                    if ($filter === 'top_voted') {
                        return $query->orderByDesc('votes_count');
                    }

                    if ($filter === 'user_ideas') {
                        return $query->where('user_id', auth()->id());
                    }
                }
            )
            ->when(
                mb_strlen($search) >= 3,
                fn ($query) => $query->where('title', 'ilike', "%$search%")
            )
            ->addSelect([ // check if user voted for idea (n+1)
                'voted_by_user' => Votable::select('id')
                    ->where('user_id', auth()->id())
                    ->whereColumn('votable_id', 'ideas.id')
            ])
            ->withCount('votes') // get votes count (n+1)
            ->latest($orderBy)
            ->simplePaginate(Idea::PAGINATION_COUNT);

        return $ideasPaginator;
    }
}
