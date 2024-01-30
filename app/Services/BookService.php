<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder;
use Nette\NotImplementedException;

class BookService
{
    /**
     * @param string $author
     * @param string $title
     * @return array<Book>
     */
    public function getBooksByAuthorOrTitle(
        string $author = null,
        string $title = null
    ): array
    {
        $query = Book::query();

        if ($title) {
            $query = $query->where('title', 'ILIKE', "%$title%");
        }

        if ($author) {
            $authorString = trim(
                array_reduce(
                    array_filter(explode(' ', $author), fn($w) => (bool)$w),
                    fn ($acc, $word) => $acc .= (' '.$word),
                    ''
                )
            );

            $query->whereHas('authors', function ($subQuery) use ($authorString) {
               return $subQuery->where('fullname', 'ILIKE', "%$authorString%");
            });
        }

        return $query->get()->toArray();
    }

    /**
     * @param int $id
     * @return array
     *
     * @throws ModelNotFoundException
     */
    public function getById(int $id): array
    {
        return Book::query()->findOrFail($id)->toArray();
    }

    public function create(array $data): int
    {
        throw new NotImplementedException();
    }

    public function update(array $data): bool
    {
        throw new NotImplementedException();
    }

    public function delete(int $id): bool
    {
        throw new NotImplementedException();
    }
}
