<?php

namespace McklayiN\QueryFilter\Tests\Filters;

use McklayiN\QueryFilter\QueryFilter;

class PostEqualsFilter extends QueryFilter
{
    public function category($value)
    {
        return $this->builder->where('category', $value);
    }
}
