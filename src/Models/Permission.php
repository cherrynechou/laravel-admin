<?php
namespace CherryneChou\Admin\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Permission extends Model
{
    protected $guarded = ['id'];

    /**
     * @var array
     */
    public static $httpMethods = [
        'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD',
    ];

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
        $this->setTable(config('admin.database.permissions_table'));
    }


    /**
     * Permission belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_permissions_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'role_id');
    }

    /**
     * @return BelongsToMany
     */
    public function menus(): BelongsToMany
    {
        $pivotTable = config('admin.database.permission_menu_table');

        $relatedModel = config('admin.database.menu_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'menu_id')->withTimestamps();
    }

    /**
     * @param  string  $path
     * @return mixed
     */
    public function getHttpPathAttribute($path)
    {
        return explode(',', $path ?? '');
    }

    /**
     * @param $path
     */
    public function setHttpPathAttribute($path)
    {
        if (is_array($path)) {
            $path = implode(',', $path);
        }

        return $this->attributes['http_path'] = $path;
    }

    /**
     * If request should pass through the current permission.
     */
    public function shouldPassThrough(Request $request): bool
    {
        if (empty($this->http_method) && empty($this->http_path)) {
            return true;
        }

        $method = $this->http_method;

        $matches = array_map(function ($path) use ($method) {
            $path = trim(config('admin.route.prefix'), '/') . $path;
            if (Str::contains($path, ':')) {
                [ $method, $path ] = explode(':', $path);
                $method = explode(',', $method);
            }
            return compact('method', 'path');
        }, $this->http_path);

        foreach ($matches as $match) {
            if ($this->matchRequest($match, $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * If a request match the specific HTTP method and path.
     */
    protected function matchRequest(array $match, Request $request): bool
    {
        if ($match['path'] == '/') {
            $path = '/';
        } else {
            $path = trim($match['path'], '/');
        }

        if (! $request->is($path)) {
            return false;
        }

        $method = collect($match['method'])->filter()->map(function ($method) {
            return strtoupper($method);
        });

        return $method->isEmpty() || $method->contains($request->method());
    }

    /**
     * @param $method
     */
    public function setHttpMethodAttribute($method)
    {
        if (is_array($method)) {
            $this->attributes['http_method'] = implode(',', $method);
        }
    }

    /**
     * @param $method
     * @return array
     */
    public function getHttpMethodAttribute($method)
    {
        if (is_string($method)) {
            return array_filter(explode(',', $method));
        }

        return $method;
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->roles()->detach();
        });
    }
}