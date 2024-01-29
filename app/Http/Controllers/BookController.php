<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    public function list(): Response
    {
        return new JsonResponse('list');
    }

    public function detail(int $id): Response
    {
        return new JsonResponse('detail_book');
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
