<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{

    /**
     * 'Фантастика' => 'science-fiction',
     * 'Приключения' => 'adventure',
     * 'Романтика' => 'romance',
     * 'Детектив' => 'detective',
     * 'Классика' => 'classic',
     */
    const CATEGORIES = [
        'science-fiction' => [
            'title' => 'Фантастика',
            'description' => 'Книги с элементами фантастики и научной фантастики.',
            'is_hidden' => false,
        ],
        'romance' => [
            'title' => 'Романтика',
            'description' => 'Книги с романтическим сюжетом и отношениями.',
            'is_hidden' => false,
        ],
        'detective' => [
            'title' => 'Детектив',
            'description' => 'Книги с захватывающими детективными сюжетами.',
            'is_hidden' => false,
        ],
        'adventure' => [
            'title' => 'Приключения',
            'description' => 'Книги с захватывающими приключенческими сюжетами.',
            'is_hidden' => false,
        ],
        'classic' => [
            'title' => 'Классика',
            'description' => 'Классические литературные произведения.',
            'is_hidden' => false,
        ]
    ];

    const BOOKS = [
        [
            'title' => 'Путешествие в неведомое',
            'published_at' => 1643568000,
            'description' => 'Увлекательное приключение в далекий мир фантастики.',
            'cover' => 'path/to/cover1.jpg',
            'is_hidden' => false,
            'owner_id' => 1,
            'f_categories' => ['science-fiction', 'adventure'],
            'f_authors' => ['jane_doe', 'john_smith'],
        ],
        [
            'title' => 'Романтический вечер',
            'published_at' => 1654080000,
            'description' => 'Сказочная история о встрече двух сердец в мире романтики.',
            'cover' => 'path/to/cover2.jpg',
            'is_hidden' => false,
            'owner_id' => 2,
            'f_categories' => ['romance'],
            'f_authors' => ['john_smith', 'alice_jones'],
        ],
        [
            'title' => 'Тайна детектива Смита',
            'published_at' => 1656662400,
            'description' => 'Раскрывайте тайны вместе с детективом Смитом.',
            'cover' => 'path/to/cover3.jpg',
            'is_hidden' => false,
            'owner_id' => 2,
            'f_categories' => ['detective', 'adventure'],
            'f_authors' => ['jane_doe'],
        ],
        [
            'title' => 'Похождения Робинзона Крузо',
            'published_at' => 1643568000,
            'description' => 'Захватывающие приключения на необитаемом острове.',
            'cover' => 'path/to/cover4.jpg',
            'is_hidden' => false,
            'owner_id' => 1,
            'f_categories' => ['adventure', 'classic'],
            'f_authors' => ['john_smith', 'alice_jones'],
        ],
        [
            'title' => 'Преступление и наказание',
            'published_at' => 1649020800,
            'description' => 'Классический роман о моральных дилеммах и наказании.',
            'cover' => 'path/to/cover5.jpg',
            'is_hidden' => false,
            'owner_id' => 2,
            'f_categories' => ['detective', 'classic'],
            'f_authors' => ['alice_jones'],
        ],
    ];

    const AUTHORS_DATA = [
        'jane_doe' => [
            'fullname' => 'Jane Doe',
            'country' => 'United States',
            'comment' => 'Renowned author of mystery novels.',
        ],
        'john_smith' => [
            'fullname' => 'John Smith',
            'country' => 'United Kingdom',
            'comment' => 'Bestselling author known for fantasy literature.',
        ],
        'alice_jones' => [
            'fullname' => 'Alice Jones',
            'country' => 'Canada',
            'comment' => 'Up-and-coming author specializing in romantic fiction.',
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->flushData();

         \App\Models\User::factory()->create([
             'name' => 'test_user',
             'email' => 'test_user@example.com',
             'role' => 'user'
         ]);

        \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);

        $categoriesDbInsertResult = array_map(function (array $category) {
            return DB::table('categories')->insertGetId($category);
        }, self::CATEGORIES);

        $authorsDbInsertResult = array_map(function (array $author) {
            return DB::table('authors')->insertGetId($author);
        }, self::AUTHORS_DATA);

        $bookData = array_map(
            function ($book) use ($categoriesDbInsertResult, $authorsDbInsertResult) {
                return [
                    ...$book,
                    'f_categories' => array_map(
                        fn ($category) => $categoriesDbInsertResult[$category],
                        $book['f_categories']
                    ),
                    'f_authors' => array_map(
                        fn ($author) => $authorsDbInsertResult[$author],
                        $book['f_authors']
                    )
                ];
            },
            self::BOOKS
        );

        $booksDbInsertResult = array_map(function (array $book) {
            $bookData = array_filter($book, fn($key) => !str_starts_with($key, 'f_'), ARRAY_FILTER_USE_KEY);
            $bookId = DB::table('books')->insertGetId(
                [
                    ...$bookData,
                    'published_at' => Carbon::createFromTimestamp($bookData['published_at'])
                ]
            );

            return [
                'id' => $bookId,
                ...$book
            ];
        }, $bookData);

        foreach ($booksDbInsertResult as $book) {
            array_map(
                fn($authorId) => DB::table('author_book')->insert([
                    'book_id' => $book['id'],
                    'author_id' => $authorId
                ]),
                $book['f_authors']
            );

            array_map(
                fn($categoryId) => DB::table('book_category')->insert([
                    'category_id' => $book['id'],
                    'book_id' => $categoryId
                ]),
                $book['f_categories']
            );
        }
    }

    private function flushData()
    {
        DB::table('users')->truncate();
        DB::table('author_book')->truncate();
        DB::table('book_category')->truncate();
        DB::table('books')->truncate();
        DB::table('authors')->truncate();
        DB::table('categories')->truncate();
    }
}
