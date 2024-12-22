<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Support\Helper;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Transformers\PermissionTransformer;
use CherryneChou\Admin\Models\Permission;
use CherryneChou\Admin\Traits\RestfulResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    use RestfulResponse;

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        $resources = Permission::query()->orderBy('order')->get();

        $permissionResources = fractal()
            ->collection($resources)
            ->transformWith(new PermissionTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();

        $permissions = Helper::listToTree($permissionResources);

        return $this->success($permissions);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function store()
    {
        $validator = $this->validateForm();

        if($validator->fails()){
            $warning = $validator->messages()->first();
            return $this->failed($warning);
        }

        try {
            DB::beginTransaction();

            Permission::create(request()->all());

            DB::commit();

            return $this->success();

        }catch (\Exception $exception){
            DB::rollBack();

            return $this->failed($exception->getTraceAsString());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function show($id)
    {
        $resource = Permission::query()->find($id);

        $permission = fractal()
                        ->item($resource)
                        ->transformWith(new PermissionTransformer())
                        ->serializeWith(new DataArraySerializer())
                        ->toArray();

        return $this->success($permission);
    }

    /***
     * 获取http_path路径
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function routes()
    {
        $prefix = (string) config('admin.route.prefix');

        $container = collect();

        $routes = collect(app('router')->getRoutes())->map(function ($route) use ($prefix, $container) {

            if (! Str::startsWith($uri = $route->uri(), $prefix) && $prefix && $prefix !== '/') {

                return false;
            }

            if (! Str::contains($uri, '{')) {

                if ($prefix !== '/') {

                    $route = Str::replaceFirst($prefix, '', $uri.'*');

                } else {

                    $route = $uri.'*';
                }

                if ($route !== '*') {

                    $container->push($route);

                }
            }

            $path = preg_replace('/{.*}+/', '*', $uri);

            if ($prefix !== '/') {

                return Str::replaceFirst($prefix, '', $path);

            }

            return $path;
        });

        $result = $container->merge($routes)->filter()->toArray();

        return $this->success( array_unique($result) );
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function all()
    {
        $resources = Permission::query()->get();

        $permissions = fractal()
                        ->collection($resources)
                        ->transformWith(new PermissionTransformer())
                        ->serializeWith(new DataArraySerializer())
                        ->toArray();

        return $this->success($permissions);
    }

        /**
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    protected function validateForm()
    {
        $rules = [
            'name'      => [
                'required',
                Rule::unique(config('admin.database.permissions_model'))->where('name',request()->input('name'))
            ],
            'slug'      => [
                'required',
                Rule::unique(config('admin.database.permissions_model'))->where('slug',request()->input('slug'))
            ],
        ];

        $message = [
            'required'      => trans('validation.attribute_not_empty'),
            'name.unique'   => trans('validation.attribute_exists'),
            'slug.unique'   => trans('validation.attribute_exists')
        ];

        $attributes = [
            'name'                      => trans('admin.permission.name'),
            'slug'                      => trans('admin.permission.slug'),
        ];

        return Validator::make(request()->all(), $rules, $message, $attributes);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function update($id)
    {
        try {
            DB::beginTransaction();

            Permission::query()->find($id)->update(request()->all());

            DB::commit();

            return $this->success();

        }catch (\Exception $exception){
            DB::rollBack();

            return $this->failed($exception->getTraceAsString());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function destroy($id)
    {
        try {

            DB::beginTransaction();

            Permission::destroy($id);

            DB::commit();

            return $this->success();

        }catch (\Exception $exception){

            DB::rollBack();

            return $this->failed($exception->getMessage());
        }
    }
}