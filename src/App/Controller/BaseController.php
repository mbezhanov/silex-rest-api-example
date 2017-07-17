<?php

namespace App\Controller;

use App\Representation\ApiProblemRepresentation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController
{
    protected function createApiProblem($type, $extraData = [], $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $apiProblem = new ApiProblemRepresentation($type, $statusCode);

        foreach ($extraData as $key => $value) {
            $apiProblem->set($key, $value);
        }

        return new JsonResponse($apiProblem->toArray(), $apiProblem->getStatusCode(), [
            'Content-Type' => 'application/problem+json'
        ]);
    }

    protected function createApiResponse($data = null, $statusCode = Response::HTTP_OK, $headers = []): Response
    {
        if (!is_null($data)) {
            $headers['Content-Type'] = 'application/hal+json';
        }

        return new Response($data, $statusCode, $headers);
    }
}
