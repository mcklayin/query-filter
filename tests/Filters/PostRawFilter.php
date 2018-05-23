<?php

namespace McklayiN\QueryFilter\Tests\Filters;

use McklayiN\QueryFilter\QueryFilter;

class PostRawFilter extends QueryFilter
{
    public function isLong()
    {
        return $this->builder->whereRaw('LENGTH(category) > ?', [10]);
    }
}
