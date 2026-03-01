<?php
namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Models\DictData;
use CherryneChou\Admin\Filters\DictDataFilter;
use CherryneChou\Admin\Transformers\DictDataTransformer;
use CherryneChou\Admin\Serializer\DataArraySerializer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use CherryneChou\Admin\Traits\RestfulResponse;
use CherryneChou\Contracts\ValidatorInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class DictDataController extends Controller
{
    use RestfulResponse;

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'label'      => 'required',
            'value'      => 'required', 
        ],
        ValidatorInterface::RULE_UPDATE => [
            'label'      => 'required',
            'value'      => 'required', 
        ]
    ];

    public function index(DictDataFilter $filter)
    {
        $dictDataPaginator = DictData::filter($filter)->paginate();

        $resources = $dictDataPaginator->getCollection();

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
        $validator = $this->validateForm(ValidatorInterface::RULE_CREATE);

        if($validator->fails()){
            $warning = $validator->messages()->first();
            return $this->failed($warning);
        }

        $requestData = request()->all();

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
        $validator = $this->validateForm(ValidatorInterface::RULE_UPDATE);

        if($validator->fails()){
            $warning = $validator->messages()->first();
            return $this->failed($warning);
        }
        
 
        $requestData = request()->all();
        
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
    protected function validateForm($rule)
    {

        $message = [
            'required'   => trans('validation.attribute_not_empty')
        ];


        $attributes = [
            'label'      => trans('admin.dict.label'),
            'value'      => trans('admin.dict.value')    
        ];

        return Validator::make(request()->all(), $this->rules[$rule], $message, $attributes);
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

