<?php

namespace McklayiN\QueryFilter\Tests\Filters;

use McklayiN\QueryFilter\QueryFilter;

class PostOptionalParameter extends QueryFilter
{
    public function category($value = 'foo')
    {
        $this->where('category', '=', $value);
    }
}
