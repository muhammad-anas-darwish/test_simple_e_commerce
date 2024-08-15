<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected $model)
    {
    }

    public function all()
    {
        return $this->model->all();
    }

    public function allWithPaginate($countPerPage = 20)
    {
        info($this->model);
        return $this->model->paginate($countPerPage);
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
    
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }
    
    public function update($id, array $attributes)
    {
        $model = $this->model->findOrFail($id);
        $model->update($attributes);
        return $model;
    }
    
    public function delete($id)
    {
        $model = $this->model->findOrFail($id);
        $model->delete();
        return $model;
    }
}