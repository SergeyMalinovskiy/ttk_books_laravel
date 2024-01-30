<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    public function create(Request $request): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title'         => 'required|string|max:150',
                'published_at'  => 'required|date_format:d-m-Y',
                'description'   => 'required|string|max:500',
                'is_hidden'     => 'boolean',

                'categories'    => 'required|array',
                'categories.*'  => 'integer',
                'authors'       => 'required|array',
                'authors.*'     => 'integer'
            ]
        );

        if ($validator->fails()) {
            return new JsonResponse($validator->errors(), 400);
        }

        return new JsonResponse(
            $this->bookService->create($request->all())
        );
    }

    public function edit(Request $request, int $id): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title'         => 'string|max:150',
                'published_at'  => 'date_format:d-m-Y',
                'description'   => 'string|max:500',
                'is_hidden'     => 'boolean',

                'categories'    => 'array',
                'categories.*'  => 'integer',
                'authors'       => 'array',
                'authors.*'     => 'integer'
            ]
        );

        if ($validator->fails() || !$request->all()) {
            $errors = $validator->errors();
            return new JsonResponse($errors->isEmpty() ? ['Parameters are empty!'] : $errors, 400);
        }

        return new JsonResponse($this->bookService->update($id, $request->all()));
    }
}
