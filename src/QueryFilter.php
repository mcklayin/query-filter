<?php

namespace McklayiN\QueryFilter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use ReflectionMethod;
use ReflectionParameter;
use McklayiN\QueryFilter\Interfaces\QueryFilter as QueryFilterInterface;

abstract class QueryFilter implements QueryFilterInterface
{
    /**
     * The request object.
     *
     * @var Request
     */
    protected $request;

    /**
     * The builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Default filter method
     *
     * @var string
     */
    protected $fallbackFilter = 'default';

    /**
     * Prefix for filter preparing method
     *
     * @var string
     */
    protected $prepareFilterNamePrefix = 'prepareFilter';

    /**
     * Prepare filter value method name
     *
     * @var string
     */
    protected $prepareFilterValueName = 'prepareValue';

    /**
     * Create a new QueryFilter instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters to the builder.
     *
     * @param  Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        if (empty($this->filters()) && method_exists($this, $this->fallbackFilter)) {
            call_user_func([$this, $this->fallbackFilter]);
        }

        foreach ($this->filters() as $name => $value) {
            $methodName = $this->prepareFilterName($name);
            $value = $this->prepareFilterValue($value);

            if ($this->shouldCall($methodName, $value)) {
                call_user_func_array([$this, $methodName], $value);
            }
        }

        return $this->builder;
    }

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
     * @param string $methodName
     * @return string
     */
    public function prepareFilterName(string $methodName): string
    {
        $prepareFilterName = $this->prepareFilterNamePrefix . ucfirst($methodName);

        if (method_exists($this, $prepareFilterName)) {
            return call_user_func([$this, $prepareFilterName], $methodName);
        }

        return camel_case($methodName);
    }

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
    public function prepareFilterValue($value): array
    {
        if (method_exists($this, $this->prepareFilterValueName)) {
            return call_user_func([$this, $this->prepareFilterValueName], $value);
        }

        return array_filter([$value]);
    }

    /**
     * Get all request filters
     *
     * @return array
     */
    protected function filters(): array
    {
        return array_filter(
            array_map('trim', $this->request->all())
        );
    }

    /**
     * Helper for "LIKE" filter
     *
     * @param  String $column
     * @param  String $value
     * @return Builder
     */
    protected function like($column, $value): Builder
    {
        if ($this->builder->getQuery()->getConnection()->getDriverName() == 'pgsql') {
            return $this->builder->where($column, 'ILIKE', '%' . $value . '%');
        }

        return $this->builder->where($column, 'LIKE', '%' . $value . '%');
    }

    /**
     * Sort the collection by the sort field
     * Examples: sort=title,asc;status,desc || sort=title,desc;status,asc
     *
     * @param string $value
     */
    protected function sort(string $value)
    {
        collect(explode(';', $value))->mapWithKeys(function (string $field) {
            list($field, $order) = explode(',', $field);

            if (in_array($order, ['asc', 'desc'])) {
                return [$field => $order];
            }

            return [$field => 'asc'];

        })->each(function (string $order, string $field) {
            $this->builder->orderBy($field, $order);
        });
    }

    /**
     * Make sure the method should be called
     *
     * @param string $methodName
     * @param array $value
     * @return bool
     */
    protected function shouldCall($methodName, array $value): bool
    {
        if (!method_exists($this, $methodName)) {
            return false;
        }

        $method = new ReflectionMethod($this, $methodName);
        /** @var ReflectionParameter $parameter */
        $parameter = Arr::first($method->getParameters());

        return $value ? $method->getNumberOfParameters() > 0 :
            $parameter === null || $parameter->isDefaultValueAvailable();
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->builder, $name)) {
            return call_user_func_array([$this->builder, $name], $arguments);
        }
    }
}
