<?php
namespace App\Admin\Controllers;

use App\Contracts\Repositories\DictRepository;
use App\Transformers\DictTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class DictController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        //添加过滤
        $dictPaginator = $this->dictRepository->paginate();

        $resources = $dictPaginator->getCollection();

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
    public function store(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        //
        $validator = $this->validateForm();

        if($validator->fails()){

            $warnings = $validator->messages();
            $show_warning = $warnings->first();

            return $this->failed($show_warning);
        }

        $requestData = request()->only([
            'name','code','type','sort','status'
        ]);

        try {
            DB::beginTransaction();

            $this->dictRepository->create($requestData);

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
        $resource =  $this->dictRepository->find($id);

        $dict = fractal()
            ->item($resource)
            ->transformWith(new DictTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();

        return $this->success($dict);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $validator = $this->validateForm();

        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return $this->failed($show_warning);
        }

        $requestData = request()->only([
            'name','code','type','sort','status'
        ]);

        //
        try {
            DB::beginTransaction();

            $dict = $this->dictRepository->find($id);

            $dict->update($requestData);

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
    protected function validateForm()
    {
        $rules = [
            'name'      => 'required',
        ];

        $message = [
            'required'      => ':attribute 不能为空',
        ];

        $attributes = [
            'name'                 => '单位名称',
        ];

        return Validator::make(request()->all(), $rules, $message, $attributes);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        //
        try {
            DB::beginTransaction();

            $this->dictRepository->delete($id);

            DB::commit();

            return $this->success();

        }catch (\Exception $exception){
            DB::rollBack();

            return $this->failed($exception->getTraceAsString());
        }
    }

}

