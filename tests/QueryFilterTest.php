<?php

namespace McklayiN\QueryFilter\Tests;

use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;

class QueryFilterTest extends TestCase
{
    public function testLikeFilterApplies()
    {
        $builder = $this->makeBuilder(Filters\PostLikeFilter::class);

        $expected = [
            "type" => "Basic",
            "column" => "title",
            "operator" => "LIKE",
            "value" => "%foo%",
            "boolean" => "and",
        ];

        $this->assertContains($expected, $builder->getQuery()->wheres);
    }

    public function testEqualsFilterApplies()
    {
        $builder = $this->makeBuilder(Filters\PostEqualsFilter::class);

        $expected = [
            "type" => "Basic",
            "column" => "category",
            "operator" => "=",
            "value" => "bar",
            "boolean" => "and",
        ];

        $this->assertContains($expected, $builder->getQuery()->wheres);
    }

    public function testRawFilterApplies()
    {
        $builder = $this->makeBuilder(Filters\PostRawFilter::class);

        $expected = [
            "type" => "raw",
            "sql" => "LENGTH(category) > ?",
            "boolean" => "and",
        ];

        $this->assertContains($expected, $builder->getQuery()->wheres);
    }

    public function testTwoFiltersApplies()
    {
        $builder = $this->makeBuilder(Filters\PostTwoFilters::class);

        $expected = [
            [
                "type" => "Basic",
                "column" => "title",
                "operator" => "LIKE",
                "value" => "%foo%",
                "boolean" => "and",
            ],
            [
                "type" => "Basic",
                "column" => "category",
                "operator" => "=",
                "value" => "bar",
                "boolean" => "and",
            ],
        ];

        $this->assertArraySubset($expected, $builder->getQuery()->wheres);
    }

    public function testNoFilterApplies()
    {
        $builder = $this->makeBuilder(Filters\PostNoFilter::class);

        $this->assertempty($builder->getQuery()->wheres);
    }

    public function testCallingBuilderMethods()
    {
        $builder = $this->makeBuilder(Filters\PostWhereFilter::class);

        $expected = [
            [
                "type" => "Basic",
                "column" => "title",
                "operator" => "like",
                "value" => "%foo%",
                "boolean" => "and",
            ],
            [
                "type" => "Basic",
                "column" => "age",
                "operator" => ">=",
                "value" => 18,
                "boolean" => "and",
            ],
        ];

        $this->assertArraySubset($expected, $builder->getQuery()->wheres);
    }

    public function testCannotAcceptEmptyValuesIfAParameterIsRequired()
    {
        $request = new Request;
        $request->merge(['category' => '']);

        $builder = $this->makeBuilder(Filters\PostTwoFilters::class, $request);

        $this->assertEmpty($builder->getQuery()->wheres);
    }

    public function testEmptyValuesAreAllowedIfThereIsAnOptionalParameter()
    {
        $request = new Request;
        $request->merge(['category' => '']);

        $builder = $this->makeBuilder(Filters\PostOptionalParameter::class, $request);

        $expected = [
            [
                "type" => "Basic",
                "column" => "category",
                "operator" => "=",
                "value" => "foo",
                "boolean" => "and",
            ],
        ];

        $this->assertNotEmpty($builder->getQuery()->wheres);
        $this->assertArraySubset($expected, $builder->getQuery()->wheres);
    }

    /**
     * @return Request
     */
    protected function makeRequest()
    {
        $request = new Request;

        $request->merge([
            'title' => 'foo',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nemo, adipisci!',
            'category' => 'bar',
            'is_long' => null,
            'age' => 18,
        ]);

        return $request;
    }

    /**
     * @param $className
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeBuilder($className, Request $request = null)
    {
        $request = $request ?: $this->makeRequest();

        $filters = new $className($request);

        return Models\Post::filter($filters);
    }
}
