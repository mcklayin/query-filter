<?php

namespace McklayiN\QueryFilter\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterableTrait
{
    /**
     * Filter a result set.
     *
     * @param  Builder      $query
     * @param  QueryFilter $filter
     * @return Builder
     */
    public function scopeFilter($query, QueryFilter $filter)
    {
        return $filter->apply($query);
    }
}
