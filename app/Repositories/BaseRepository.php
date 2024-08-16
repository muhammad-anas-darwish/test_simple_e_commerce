<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    /**
     * BaseRepository constructor.
     *
     * @param Model $model The model instance to be used in this repository.
     */
    public function __construct(protected $model)
    { }

    /**
     * Retrieve all records of the model.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[] All records of the model.
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Retrieve all records with pagination.
     *
     * @param int $countPerPage Number of records per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Paginated records.
     */
    public function allWithPaginate($countPerPage = 20)
    {
        info($this->model);
        return $this->model->paginate($countPerPage);
    }

    /**
     * Find a specific record by its ID.
     *
     * @param int $id The ID of the record.
     * @return Model The found record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the record is not found.
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
    
    /**
     * Create a new record with the given attributes.
     *
     * @param array $attributes The attributes to create a new record.
     * @return Model The newly created record.
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }
    
    /**
     * Update a specific record by its ID with the given attributes.
     *
     * @param int $id The ID of the record to update.
     * @param array $attributes The attributes to update the record.
     * @return Model The updated record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the record is not found.
     */
    public function update($id, array $attributes)
    {
        $model = $this->model->findOrFail($id);
        $model->update($attributes);
        return $model;
    }
    
    /**
     * Delete a specific record by its ID.
     *
     * @param int $id The ID of the record to delete.
     * @return Model The deleted record.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the record is not found.
     */
    public function delete($id)
    {
        $model = $this->model->findOrFail($id);
        $model->delete();
        return $model;
    }
}