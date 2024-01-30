<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    private BookService $bookService;

    public function __construct(
        BookService $bookService
    )
    {
        $this->bookService = $bookService;
    }
    public function list(Request $request): Response
    {
        return new JsonResponse(
            $this->bookService->getBooksByAuthorOrTitle(
                $request->query->get('author'),
                $request->query->get('title')
            )
        );
    }

    public function detail(int $id): Response
    {
        return new JsonResponse($this->bookService->getById($id));
    }

    public function create(): Response
    {
        return new JsonResponse('create_book');
    }

    public function edit(int $id): Response
    {
        return new JsonResponse('edit book');
    }

    public function delete(int $id): Response
    {
        return new JsonResponse('book_deletion');
    }
}
