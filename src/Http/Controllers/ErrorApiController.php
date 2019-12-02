<?php

namespace Laracatch\Client\Http\Controllers;

use Illuminate\Http\Request;
use Laracatch\Client\Contracts\StorageContract;

class ErrorApiController
{
    /**
     * @var StorageContract
     */
    protected $storage;

    /**
     * @param StorageContract $storage
     */
    public function __construct(StorageContract $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->storage->get($request->get('filters', []));

        return response()->json($data);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->storage->find($id);

        return response()->json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear()
    {
        $this->storage->clear();

        return response()->json([]);
    }
}
