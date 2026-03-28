<?php
namespace CherryneChou\Admin\Traits;

trait WithAttributes
{
	/**
     * @var string
     */
    protected string $parentColumn = 'parent_id';

    /**
     * @var string
     */
    protected string $titleColumn = 'title';

    /**
     * @var string
     */
    protected string $orderColumn = 'sort';

    /**
     * @var string
     */
    protected string $idColumn = 'id';
}