<?php

namespace App\Http\Controllers;

use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
use App\Traits\ApiResponses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    use ApiResponses;

    protected $resourceClass;
    protected $modelClass;
    
    abstract protected function storeRequest(): string;
    abstract protected function updateRequest(): string;

    protected $repository;
   
    public function __construct()
    {
        $this->repository = new BaseRepository(new $this->modelClass);
    }

    public function index()
    {
        $items = $this->repository->allWithPaginate(20);
        
        return $this->paginatedResponse($items, $this->resourceClass);
    }

    public function store(Request $request)
    {
        $request = app($this->storeRequest());
        $validatedData = $request->validate($request->rules());
        $item = $this->repository->create($validatedData);
        return $this->successResponse(new $this->resourceClass($item), 'Resource created successfully', 201);
    }

    public function show($id)
    {
        $model = $this->repository->find($id);
        return $this->successResponse(new $this->resourceClass($model), 'Resource retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $request = app($this->updateRequest());
        $validatedData = $request->validate($request->rules());
        $item = $this->repository->update($id, $validatedData);
        return $this->successResponse(new $this->resourceClass($item), 'Resource updated successfully');
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return $this->noContentResponse();
    }
}
