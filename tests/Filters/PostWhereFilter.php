<?php

namespace McklayiN\QueryFilter\Tests\Filters;

use McklayiN\QueryFilter\QueryFilter;

class PostWhereFilter extends QueryFilter
{
    public function title($value)
    {
        $this->where('title', 'like', "%$value%");
    }

    public function age($value)
    {
        $this->where('age', '>=', $value);
    }
}
