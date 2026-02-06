<?php
namespace App\Admin\Controllers;

use App\Contracts\Repositories\DictDataRepository;
use Illuminate\Http\Request;

class DictDataController extends Controller
{
    private DictDataRepository $dictDataRepository;

    public function __construct(DictDataRepository $dictDataRepository)
    {
        $this->dictDataRepository = $dictDataRepository;
    }


    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * @return \Illuminate\Validation\Validator
     */
    protected function validateForm()
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }
}

