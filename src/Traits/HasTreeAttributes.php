<?php

namespace CherryneChou\Admin\Traits;

trait HasTreeAttributes
{
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
}