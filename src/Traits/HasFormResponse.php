<?php
namespace CherryneChou\Admin\Traits;

trait HasFormResponse
{
	/**
     * 返回字段验证错误信息.
     *
     * @param  array|MessageBag|\Illuminate\Validation\Validator  $validationMessages
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function validationErrorsResponse($validationMessages)
    {
        return $this
            ->response()
            ->withValidation($validationMessages)
            ->send();
    }
}