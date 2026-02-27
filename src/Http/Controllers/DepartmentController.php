<?php
namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\Department;
use CherryneChou\Admin\Filters\DepartmentFilter;
use CherryneChou\Admin\Support\Helper;
use CherryneChou\Admin\Transformers\DepartmentTransformer;
use Illuminate\Routing\Controller;
use CherryneChou\Admin\Traits\RestfulResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use CherryneChou\Admin\Serializer\DataArraySerializer;


class DepartmentController extends Controller
{
	 use RestfulResponse;

    public function index(DepartmentFilter $filter): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $resources = Department::filter($filter)->get();

        $departmentResources = fractal()
            ->collection($resources)
            ->transformWith(new DepartmentTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();

        $departments = Helper::listToTree($departmentResources);
                
        return $this->success($departmentDatas);
    }


     /**
     * Store a newly created resource in storage.
     */
    public function store(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        //
        $validator = $this->validateForm();

        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return $this->failed($show_warning);
        }

        $requestData = request()->only([
            'name','parent_id','principal','email','telephone','sort','status'
        ]);

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
        return $this->success($dict);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $validator = $this->validateForm();

        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return $this->failed($show_warning);
        }

        $requestData = request()->only([
            'name','parent_id','principal','email','telephone','sort','status'
        ]);

        //
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