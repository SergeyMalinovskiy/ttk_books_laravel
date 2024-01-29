<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthorController
{
    public function create(): Response
    {
        return new JsonResponse('data');
    }

    public function edit(): Response
    {
        return new JsonResponse('data');
    }
}
