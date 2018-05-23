<?php

namespace McklayiN\QueryFilter\Tests\Filters;

use McklayiN\QueryFilter\QueryFilter;

class PostLikeFilter extends QueryFilter
{
    public function title($value)
    {
        return $this->like('title', $value);
    }
}
