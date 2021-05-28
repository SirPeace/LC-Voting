<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ShouldAuthorize
{
    /**
     * Abort response if not authenticated or authorized
     *
     * @param callable $ability
     * @return void
     */
    protected function authorize(callable $ability): void
    {
        if (auth()->guest() || !$ability()) {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
