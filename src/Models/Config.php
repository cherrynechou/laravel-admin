<?php

namespace CherryneChou\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use CherryneChou\Admin\Traits\HasScopeFilterable;

/**
 * Class SystemConfig.
 *
 * @package namespace App\Models;
 */
class Config extends Model 
{
    use HasScopeFilterable;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $connection = config('admin.database.connection') ?: config('database.default');
        $this->setConnection($connection);
        $this->setTable(config('admin.database.config_table'));
    }
}