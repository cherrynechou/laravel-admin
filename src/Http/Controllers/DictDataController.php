<?php
namespace App\Admin\Controllers;

use CherryneChou\Admin\Models\DictData;
use App\Transformers\DictDataTransformer;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class DictDataController extends Controller
{

    public function index(DictDataFilter $filter)
    {
        $dictDataPaginator = DictData::filter($filter)->paginate();

        $resources = $adminPaginator->getCollection();

        $dictDatas = fractal()
            ->collection($resources)
            ->transformWith(new DictDataTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($dictDataPaginator))
            ->toArray();


        return $this->success($dictDatas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $this->validateForm();

        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return $this->failed($show_warning);
        }

        $requestData = $request->only([
            'dict_id','code','label','value','is_default','status','sort','remark'
        ]);

        //查询当前
        $codeData= DictData::ofCode($requestData['code'])->get();

        if(count($codeData) == 0){
            $requestData['is_default'] = 1;
        }

        try {
            DB::beginTransaction();

            DictData::create($requestData);

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
        //获取资源
        $resource =  DictData::query()->find($id);

        $dict = fractal()
            ->item($resource)
            ->transformWith(new DictDataTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();

        return $this->success($dict);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = $this->validateForm();

        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return $this->failed($show_warning);
        }
        
        //
        $requestData = request()->only([
            'dict_id','code','label','value','is_default','status','sort','remark'
        ]);

        //
        try {
            DB::beginTransaction();

            $dict = DictData::query()->find($id);

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
            'label'      => 'required',
            'value'      => 'required', 
        ];

        $message = [
            'required'   => trans('validation.attribute_not_empty')
        ];


        $attributes = [
            'label'      => trans('admin.dict.label'),
            'value'      => trans('admin.dict.value')    
        ];

        return Validator::make(request()->all(), $rules, $message, $attributes);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            DB::beginTransaction();
            DictData::destroy($id);
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->failed($exception->getTraceAsString());
        }
    }
}

