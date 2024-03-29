<?php

namespace App\Services;

use App\Enum\UserRole;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Nette\NotImplementedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BookService
{
    private const PER_PAGE = 5;

    /**
     * @param string $author
     * @param string $title
     * @return array<Book>
     */
    public function getBooksByAuthorOrTitle(
        int $page = 1,
        string $author = null,
        string $title = null,
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

        return $query->paginate(self::PER_PAGE, ['*'], 'page', $page)->items();
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

    /**
     * @param array $data
     * @return int
     *
     * @throws ModelNotFoundException
     */
    public function create(array $data, int $userId): int
    {

        $authorIds = array_unique($data['authors']);
        $categoriesIds = array_unique($data['categories']);

        if (Author::query()->whereIn('id', $authorIds)->count() != count($authorIds)) {
            throw new ModelNotFoundException("Authors not found!");
        }

        if (Category::query()->whereIn('id', $categoriesIds)->count() != count($categoriesIds)) {
            throw new ModelNotFoundException("Categories not found!");
        }

        $book = new Book([
            ...$data,
            'published_at' => Carbon::createFromFormat('d-m-Y', $data['published_at']),
            'owner_id' => $userId
        ]);

        $book->save();

        $book->authors()->attach($authorIds);
        $book->categories()->attach($categoriesIds);

        return $book->id;
    }


    /**
     * @param int $id
     * @param array $data
     * @return array
     *
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data, int $userId): array
    {
        $book = Book::query()->findOrFail($id);
        /**
         * @var User $currentUser
         */
        $currentUser = User::query()->find($userId);

        if (! ($book->owner_id == $userId || $currentUser->role == UserRole::Admin->value)) {
            return [
                'data' => [
                    'message' => 'You`re dont have permission on action!',
                ],
                'code' => Response::HTTP_BAD_REQUEST,
            ];
        }

        $authorIds = array_unique($data['authors'] ?? []);
        $categoriesIds = array_unique($data['categories'] ?? []);

        if ($authorIds && (Author::query()->whereIn('id', $authorIds)->count() != count($authorIds))) {
            throw new ModelNotFoundException("Authors not found!");
        }

        if ($categoriesIds && (Category::query()->whereIn('id', $categoriesIds)->count() != count($categoriesIds))) {
            throw new ModelNotFoundException("Categories not found!");
        }

        $book->fill($data);
        $book->save();

        $book->authors()->sync($authorIds);
        $book->categories()->sync($categoriesIds);

        return ['data' => []];
    }

//    public function delete(int $id): array
//    {
//        $book = Book::query()->findOrFail($id);
//
//        if(!$book->exists()) {
//            return [];
//        }
//
//        $book->delete();
//
//        return [];
//    }
}
