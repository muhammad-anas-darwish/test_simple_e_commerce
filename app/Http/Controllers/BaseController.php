<?php

namespace App\Http\Controllers;

use App\Repositories\BaseRepository;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    use ApiResponses;

    /**
     * The resource class used for transforming model data.
     *
     * @var string
     */
    protected $resourceClass;

    /**
     * The model class used for the repository.
     *
     * @var string
     */
    protected $modelClass;
    
    /**
     * Define the request class for storing a resource.
     *
     * @return string The fully qualified class name of the store request.
     */
    abstract protected function storeRequest(): string;

    /**
     * Define the request class for updating a resource.
     *
     * @return string The fully qualified class name of the update request.
     */
    abstract protected function updateRequest(): string;

    /**
     * The repository instance for managing database interactions.
     *
     * @var BaseRepository
     */
    protected $repository;
   
    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        // Instantiate the repository with the model class
        $this->repository = new BaseRepository(new $this->modelClass);
    }

    /**
     * Display a listing of the resource with pagination.
     *
     * @return \Illuminate\Http\JsonResponse The paginated list of resources.
     */
    public function index()
    {
        $items = $this->repository->allWithPaginate(20);
        
        return $this->paginatedResponse($items, $this->resourceClass);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The incoming request.
     * @return \Illuminate\Http\JsonResponse The response after creating the resource.
     */
    public function store(Request $request)
    {
        $request = app($this->storeRequest());
        $validatedData = $request->validate($request->rules());
        $item = $this->repository->create($validatedData);
        return $this->successResponse(new $this->resourceClass($item), 'Resource created successfully', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id The ID of the resource to display.
     * @return \Illuminate\Http\JsonResponse The response containing the resource data.
     */
    public function show($id)
    {
        $model = $this->repository->find($id);
        return $this->successResponse(new $this->resourceClass($model), 'Resource retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The incoming request.
     * @param int $id The ID of the resource to update.
     * @return \Illuminate\Http\JsonResponse The response after updating the resource.
     */
    public function update(Request $request, $id)
    {
        $request = app($this->updateRequest());
        $validatedData = $request->validate($request->rules());
        $item = $this->repository->update($id, $validatedData);
        return $this->successResponse(new $this->resourceClass($item), 'Resource updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id The ID of the resource to delete.
     * @return \Illuminate\Http\JsonResponse A no-content response after deleting the resource.
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return $this->noContentResponse();
    }
}
