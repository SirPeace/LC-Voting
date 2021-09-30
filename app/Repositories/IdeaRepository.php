<?php

namespace App\Repositories;

use App\Models\Idea;
use App\Models\Status;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class IdeaRepository
{
    /**
     * Get paginated list of Idea models to render on index page
     *
     * @param string $category Category name query string
     * @param string $filter Filter option query string
     * @param string $search Search value query string
     * @param string $status Status name query string
     * @param string $orderBy Sort posts in descending order by this field
     * @return Illuminate\Pagination\Paginator
     */
    public static function getIdeasForIndex(
        string $category = '',
        string $filter = '',
        string $search = '',
        string $status = '',
        string $orderBy = 'id'
    ): Paginator {
        $categories = Category::all()->toBase();
        $user = auth()->user();

        $ideasPaginator = Idea::with('user', 'category', 'status') // eager-load relationships (n+1)
            // Statuses
            ->when(
                $status,
                function (Builder $query) use ($status) {
                    $statuses = Status::pluck('id', 'name');
                    return $query->where('status_id', $statuses[$status]);
                }
            )
            // Categories
            ->when(
                $category,
                function (Builder $query) use ($categories, $category) {
                    $categories = $categories->pluck('id', 'name');
                    return $query->where('category_id', $categories[$category]);
                }
            )
            // Filters
            ->when(
                $filter,
                function (Builder $query) use ($filter, $user) {
                    if ($filter === 'top_voted') {
                        return $query->orderByDesc('votes_count');
                    }

                    if ($filter === 'user_ideas') {
                        return $query->where('user_id', auth()->id());
                    }

                    if ($filter === 'spam' && optional($user)->isAdmin()) {
                        return $query->has('spamMarks')
                            ->withCount('spamMarks')
                            ->latest('spam_marks_count');
                    }

                    if (
                        $filter === 'comment_spam' && optional($user)->isAdmin()
                    ) {
                        return $query->withCount('spamMarks')
                            ->latest('spam_marks_count')
                            ->whereHas(
                                'comments',
                                fn (Builder $query) => $query->has('spamMarks')
                            );
                    }
                }
            )
            // Search
            ->when(
                mb_strlen($search) >= 3,
                fn (Builder $query) => (
                    $query->whereRaw(
                        "LOWER(title) LIKE '%".strtolower($search)."%'"
                    )
                )
            )
            // Check if user voted for idea (n+1)
            ->addSelect([
                'voted_by_user' => DB::table('votes')
                    ->select('id')
                    ->where('user_id', auth()->id())
                    ->whereColumn('idea_id', 'ideas.id')
            ])
            // Add votes_count and comments_count properties (n+1)
            ->withCount('votes')
            ->withCount('comments')
            ->latest($orderBy)
            ->simplePaginate()
            ->withQueryString();

        return $ideasPaginator;
    }
}
