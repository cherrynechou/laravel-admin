<?php

namespace CherryneChou\Admin\Traits;

trait HasModelTreeAttributes
{
    use SortableTrait;

    /**
     * @var array
     */
    protected static $branchOrder = [];

    /**
     * @var \Closure[]
     */
    protected $queryCallbacks = [];

    /**
     * Get title column.
     *
     * @return string
     */
    public function getIdColumn()
    {
        return property_exists($this, 'idColumn') ? $this->idColumn : 'id';
    }


	/**
     * Get title column.
     *
     * @return string
     */
    public function getTitleColumn()
    {
        return property_exists($this, 'titleColumn') ? $this->titleColumn : 'title';
    }


	/**
     * @return string
     */
    public function getParentColumn()
    {
        return property_exists($this, 'parentColumn') ? $this->parentColumn : 'parent_id';
    }


    /**
     * Get order column name.
     *
     * @return string
     */
    public function getOrderColumn()
    {
        return property_exists($this, 'orderColumn') ? $this->orderColumn : 'order';
    }

    /**
     * Get depth column name.
     *
     * @return string
     */
    public function getDepthColumn()
    {
        return property_exists($this, 'depthColumn') ? $this->depthColumn : '';
    }

    /**
     * @return string
     */
    public function getDefaultParentId()
    {
        return property_exists($this, 'defaultParentId') ? $this->defaultParentId : '0';
    }

    /**
     * Set query callback to model.
     *
     * @param  \Closure|null  $query
     * @return $this
     */
    public function withQuery(\Closure $query = null)
    {
        $this->queryCallbacks[] = $query;

        return $this;
    }


    /**
     * Set the order of branches in the tree.
     *
     * @param  array  $order
     * @return void
     */
    protected static function setBranchOrder(array $order)
    {
        static::$branchOrder = array_flip(Arr::flatten($order));

        static::$branchOrder = array_map(function ($item) {
            return ++$item;
        }, static::$branchOrder);
    }


        /**
     * Save tree order from a tree like array.
     *
     * @param  array  $tree
     * @param  int  $parentId
     */
    public static function saveOrder($tree = [], $parentId = 0, $depth = 1)
    {
        if (empty(static::$branchOrder)) {
            static::setBranchOrder($tree);
        }

        foreach ($tree as $branch) {
            $node = static::find($branch['id']);

            $node->{$node->getParentColumn()} = $parentId;
            $node->{$node->getOrderColumn()} = static::$branchOrder[$branch['id']];
            $node->getDepthColumn() && $node->{$node->getDepthColumn()} = $depth;
            $node->save();

            if (isset($branch['children'])) {
                static::saveOrder($branch['children'], $branch['id'], $depth + 1);
            }
        }
    }
}