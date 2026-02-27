<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Filters\PostFilter;
use CherryneChou\Admin\Models\Post;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Transformers\PostTransformer;
use CherryneChou\Admin\Traits\RestfulResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
	use RestfulResponse;

	public function index(PostFilter $filter): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $postPaginator = Post::filter($filter)->orderBy('sort')->paginate();

        $resources = $postPaginator->getCollection();

        $lines = fractal()
            ->collection($resources)
            ->transformWith(new DictTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($dictPaginator))
            ->toArray();

        return $this->success($lines);
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

        // 获取通过验证的数据...
        $validated = $validator->safe()->all();

        try {
            DB::beginTransaction();
            Department::create($validated);
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
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return $this->failed($show_warning);
        }

        // 获取通过验证的数据...
        $validated = $validator->safe()->all();

        try {
            DB::beginTransaction();
            $department = Department::query()->find($id);
            $department->update($validated);
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
                        ->transformWith(new PostTransformer())
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
            'name'      => trans('admin.post.name'),
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

            Post::destroy($id);

            DB::commit();

            return $this->success();

        }catch (\Exception $exception){

            DB::rollBack();

            return $this->failed($exception->getTraceAsString());
        }
    }

}