<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryService
{
    /**
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $category = new Category($data);

        $category->save();

        return $category->id;
    }

    public function update(int $id, array $data): array
    {
        $category = Category::query()->findOrFail($id);

        $category->fill($data);
        $category->save();

        return [];
    }

    public function delete(int $id): array
    {
        $category = Category::query()->findOrFail($id);

        if(!$category->exists()) {
            return [];
        }

        $category->delete();

        return [];
    }
}
