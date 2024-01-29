<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoryController
{
    public function create(): Response
    {
        return new JsonResponse('create new');
    }

    public function edit(int $id): Response
    {
        return new JsonResponse('edit data');
    }
}
