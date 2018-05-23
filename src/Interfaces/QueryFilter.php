<?php

namespace McklayiN\QueryFilter\Interfaces;

interface QueryFilter {

    /**
     * Prepare filter method name
     *
     * P.S. useful when $request parameter doesn't equal filter method name
     * and you don't want to change filter method name
     *
     * @example
     * function prepareFilterTitle($methodName) {...}
     *
     *
     * @param $methodName
     * @return string
     */
    public function prepareFilterName(string $methodName): string;

    /**
     * Prepare filters value
     *
     * P.S. useful when you need to change process of value clearing
     *
     *
     * @example
     * function prepareValue($value) {...}
     *
     * @param $value
     * @return array
     */
    public function prepareFilterValue($value): array;
}