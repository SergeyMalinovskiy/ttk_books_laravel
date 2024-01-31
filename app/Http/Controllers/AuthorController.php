<?php

namespace App\Http\Controllers;

use App\Services\AuthorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthorController
{
    private AuthorService $authorService;

    public function __construct(
        AuthorService $authorService
    )
    {
        $this->authorService = $authorService;
    }

    public function create(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'fullname'  => 'required|string|max:150',
            'country'   => 'required|string|max:100',
            'comment'   => 'string|max:500'
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($this->authorService->create($request->all()));
    }

    public function edit(Request $request, int $id): Response
    {
        $validator = Validator::make($request->all(), [
            'fullname'  => 'string|max:150',
            'country'   => 'string|max:100',
            'comment'   => 'string|max:500'
        ]);

        if ($validator->fails() || !$request->all()) {
            $errors = $validator->errors();

            return new JsonResponse(
                $errors->isEmpty() ? ['Parameters are empty!'] : $errors,
                Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            $this->authorService->update($id, $request->all())
        );
    }
}
