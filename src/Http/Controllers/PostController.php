<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Filters\PostFilter;
use CherryneChou\Admin\Models\Post;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use CherryneChou\Admin\Transformers\PostTransformer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Validation\Rule;

class PostController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
	public function index(PostFilter $filter): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $postPaginator = Post::filter($filter)->orderBy('sort')->paginate();

        $resources = $postPaginator->getCollection();

        $lines = fractal()
            ->collection($resources)
            ->transformWith(new PostTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($postPaginator))
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
            $warning =$validator->messages()->first();
            return $this->failed($show_warning);
        }

        $requestData = request()->all();

        try {
            DB::beginTransaction();
            Post::create($requestData);
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
    public function show(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        //
        $resource =  Post::query()->find($id);
        $post = fractal()
            ->item($resource)
            ->transformWith(new PostTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();
        return $this->success($post);

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
            $post = Post::query()->find($id);
            $post->update($requestData);
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
    public function all(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $resources = Post::query()->get();

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