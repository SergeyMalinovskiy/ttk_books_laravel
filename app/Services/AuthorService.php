<?php

namespace App\Services;

use App\Models\Author;

class AuthorService
{
    public function create(array $data): int
    {
        $author = new Author($data);

        $author->save();

        return $author->id;
    }

    public function update(int $id, array $data): array
    {
        $author = Author::query()->findOrFail($id);

        $author->fill($data);
        $author->save();

        return [];
    }
}
