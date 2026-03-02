<?php

namespace CherryneChou\Admin\Http\Controllers;

use CherryneChou\Admin\Traits\HasFilterData;

class ConfigController extends BaseController
{
    use HasFilterData;

    public function update($group='')
    {
        $params = request()->all();

        $updates = $this->filterEmptyOrNullData($params);

        try{

            DB::beginTransaction();

            DB::commit();

            return $this->success();

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->failed($exception->getMessage());
        }

    }

}