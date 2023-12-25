<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResponsesService
{
    public function validationError($errors)
    {
        $response = new JsonResponse(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->send();
        exit;
    }

    public function generalError() 
    {
        $response = new JsonResponse(['message' => 'Somethoing went wrong'], Response::HTTP_BAD_REQUEST);
        $response->send();
        exit;
    }

    public function fileNotFound($fileName = '') 
    {
        if ($fileName != '') {
            $fileName .= ' '; 
        }
        $response = new JsonResponse(['message' => 'File ' . $fileName . 'is not found'], Response::HTTP_NOT_FOUND);
        $response->send();
        exit;
    }

    public function success($data = ['success' => true]) 
    {
        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function unauthorized()
    {
        return new JsonResponse([
            'message' => 'missing credentials',
        ], Response::HTTP_UNAUTHORIZED);
        
    }

    
}