<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\AttachmentCategory;
use CherryneChou\Admin\Transformers\AttachmentCategoryTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use CherryneChou\Admin\Contracts\ValidatorInterface;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AttachmentCategoryController extends BaseController
{
	protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'name'      => 'required',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'name'      => 'required',
        ]
    ];

    public function index(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $attachmentCategoryPaginator = AttachmentCategory::query()->orderBy('sort')->paginate();

        $resources = $attachmentCategoryPaginator->getCollection();

        $attachments = fractal()
            ->collection($resources)
            ->transformWith(new AttachmentCategoryTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($attachmentCategoryPaginator))
            ->toArray();

        return $this->success($attachmnts);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $validator = $this->validateForm(ValidatorInterface::RULE_CREATE);

        if($validator->fails()){
            $warning = $validator->messages()->first();
            return $this->failed($warning);
        }

        $requestData = request()->all();

        try {
            DB::beginTransaction();
            AttachmentCategory::create($requestData);
            DB::commit();
            return $this->success([], trans('admin.save_succeeded'));
        }catch (\Exception $exception){
            DB::commit();
            return $this->failed($exception->getMessage());
        }
    }

    public function show(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $resource =  AttachmentCategory::query()->find($id);
        $category = fractal()
            ->item($resource)
            ->transformWith(new AttachmentCategoryTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();
        return $this->success($category);
    }

    public function update(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $validator = $this->validateForm( ValidatorInterface::RULE_UPDATE );

        if($validator->fails()){
            $warning = $validator->messages()->first();
            return $this->failed($warning);
        }

        $requestData = request()->all();

        //
        try {
            DB::beginTransaction();
            $category = AttachmentCategory::query()->find($id);
            $category->update($requestData);
            DB::commit();
            return $this->success([], trans('admin.update_succeeded'));
        }catch (\Exception $exception){
            DB::commit();
            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @return \Illuminate\Validation\Validator
     */
    protected function validateForm($rule)
    {
        $message = [
            'required'  => trans('validation.attribute_not_empty'),
        ];

        $attributes = [
            'name'      => trans('admin.attachment.category.name'),
        ];

        return Validator::make(request()->all(), $this->rules[$rule], $message, $attributes);
    }

    public function destroy(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        try {
            DB::beginTransaction();
            AttachmentCategory::destroy($id);
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->failed($exception->getTraceAsString());
        }
    }
}