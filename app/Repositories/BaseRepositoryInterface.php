<?php

namespace App\Repositories;

interface BaseRepositoryInterface 
{
    public function all();
    public function allWithPaginate($countPerPage = 20);
    public function find($id);
    public function create(array $attributes);
    public function update($id, array $attributes);
    public function delete($id);
}