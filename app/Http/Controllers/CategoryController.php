<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CategoryController
{
    private CategoryService $categoryService;

    public function __construct(
        CategoryService $categoryService
    )
    {
        $this->categoryService = $categoryService;
    }

    public function create(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|string|max:150',
            'description'   => 'required|string|max:500',
            'is_hidden'     => 'boolean'
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            $this->categoryService->create($request->all())
        );
    }

    public function edit(Request $request, int $id): Response
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'string|max:150',
            'description'   => 'string|max:500',
            'is_hidden'     => 'boolean'
        ]);

        if ($validator->fails() || !$request->all()) {
            $errors = $validator->errors();

            return new JsonResponse(
                $errors->isEmpty() ? ['Parameters are empty!'] : $errors,
                Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            $this->categoryService->update($id, $request->all())
        );
    }

    public function delete(int $id): Response
    {
        return new JsonResponse(
            $this->categoryService->delete($id)
        );
    }
}
