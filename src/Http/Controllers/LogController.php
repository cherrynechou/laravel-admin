<?php
namespace CherryneChou\Admin\Http\Controllers;

class LogController extends Controller
{
	use RestfulResponse;

	/**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        $resources = Menu::query()->orderBy('sort')->get();

        $menuResources = fractal()
                        ->collection($resources)
                        ->transformWith(new MenuTransformer())
                        ->serializeWith(new DataArraySerializer())
                        ->parseIncludes(['roles'])
                        ->toArray();

        $menus = Helper::listToTree($menuResources);

        return $this->success($menus);
    }
}