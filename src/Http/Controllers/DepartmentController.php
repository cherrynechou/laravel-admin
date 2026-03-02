<?php
namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\Department;
use CherryneChou\Admin\Filters\DepartmentFilter;
use CherryneChou\Admin\Support\Helper;
use CherryneChou\Admin\Transformers\DepartmentTransformer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use CherryneChou\Admin\Serializer\DataArraySerializer;

class DepartmentController extends BaseController
{
    public function index(DepartmentFilter $filter): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $resources = Department::filter($filter)->get();

        $departmentResources = fractal()
            ->collection($resources)
            ->transformWith(new DepartmentTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();

        $departments = Helper::listToTree($departmentResources);
                
        return $this->success($departments);
    }


    /**
    * Store a newly created resource in storage.
    */
    public function store(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        //
        $validator = $this->validateForm();

        if($validator->fails()){
        $warning = $validator->messages()->first();
            return $this->failed($warning);
        }

        $requestData = request()->all();

        try {
            DB::beginTransaction();
            Department::create($requestData);
            DB::commit();
            return $this->success([], trans('admin.save_succeeded'));
        }catch (\Exception $exception){
            DB::commit();
            return $this->failed($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $resource =  Department::query()->find($id);
        $department = fractal()
            ->item($resource)
            ->transformWith(new DepartmentTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();
        return $this->success($department);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $validator = $this->validateForm();

        if($validator->fails()){
            $warning = $validator->messages()->first();
            return $this->failed($warning);
        }

        $requestData = request()->all();
        
        try {
            DB::beginTransaction();
            $department = Department::query()->find($id);
            $department->update($requestData);
            DB::commit();
            return $this->success([], trans('admin.update_succeeded'));
        }catch (\Exception $exception){
            DB::commit();
            return $this->failed($exception->getMessage());
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function all()
    {
        $resources = Department::query()->get();

        $departments = fractal()
                        ->collection($resources)
                        ->transformWith(new DepartmentTransformer())
                        ->serializeWith(new DataArraySerializer())
                        ->toArray();

        return $this->success($departments);
    }


    /**
     * @return \Illuminate\Validation\Validator
     */
    protected function validateForm()
    {
        $rules = [
            'name'      => 'required',
        ];

        $message = [
            'required'  => trans('validation.attribute_not_empty'),
        ];

        $attributes = [
            'name'      => trans('admin.department.name'),
        ];

        return Validator::make(request()->all(), $rules, $message, $attributes);
    }



     /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function destroy($id)
    {

        try {
            DB::beginTransaction();

            Department::destroy($id);

            DB::commit();

            return $this->success();

        }catch (\Exception $exception){

            DB::rollBack();

            return $this->failed($exception->getTraceAsString());
        }
    }
}