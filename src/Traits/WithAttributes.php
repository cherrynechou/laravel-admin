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
    protected string $orderColumn = 'order';

    /**
     * @var string
     */
    protected string $idColumn = 'id';
}