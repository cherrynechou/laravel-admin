<?php
namespace CherryneChou\Admin\Abstracts;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

/**
 * Class QueryFilter
 * @package App\Abstracts
 */
abstract class QueryFilter
{
    /**
     * @var Request
     */
    protected $request;

    protected $builder;

    /**
     * QueryFilter constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                call_user_func_array([$this, $name], array_filter([$value]));
            }
        }

        return $this->builder;
    }

    public function filters()
    {
        return $this->request->all();
    }
}
