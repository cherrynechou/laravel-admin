<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\Attachment;
use CherryneChou\Admin\Transformers\AttachmentTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use CherryneChou\Admin\Contracts\ValidatorInterface;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;


class AttachmentController extends BaseController
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
        $attachmentPaginator = Attachment::query()->orderBy('sort')->paginate();

        $resources = $attachmentPaginator->getCollection();

        $attachments = fractal()
            ->collection($resources)
            ->transformWith(new AttachmentTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($attachmentPaginator))
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
            Attachment::create($requestData);
            DB::commit();
            return $this->success([], trans('admin.save_succeeded'));
        }catch (\Exception $exception){
            DB::commit();
            return $this->failed($exception->getMessage());
        }
    }

    public function show(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $resource =  Attachment::query()->find($id);
        $attachment = fractal()
            ->item($resource)
            ->transformWith(new AttachmentTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();
        return $this->success($attachment);
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
            $attachment = Attachment::query()->find($id);
            $attachment->update($requestData);
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
            'name'      => trans('admin.attachment.name'),
        ];

        return Validator::make(request()->all(), $this->rules[$rule], $message, $attributes);
    }

    public function destroy(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        try {
            DB::beginTransaction();
            Attachment::destroy($id);
            DB::commit();
            return $this->success([], trans('admin.delete_succeeded'));
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->failed($exception->getTraceAsString());
        }
    }
}