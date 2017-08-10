<?php

namespace App\Controller;

use App\Exception\ApiProblemException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController
{
    protected function createApiResponse($data = null, $statusCode = Response::HTTP_OK, $headers = []): Response
    {
        if (!is_null($data)) {
            $headers['Content-Type'] = 'application/hal+json';
        }

        return new Response($data, $statusCode, $headers);
    }

    protected function extractRequestBody(Request $request, array $expectedParameters = []): array
    {
        $requestBody = json_decode($request->getContent(), true);
        $requestBody = array_filter($requestBody, function ($value, $key) use ($expectedParameters) {
            return in_array($key, $expectedParameters) && !empty($value);
        }, ARRAY_FILTER_USE_BOTH);

        if (count($requestBody) !== count($expectedParameters)) {
            throw new ApiProblemException(ApiProblemException::TYPE_INVALID_REQUEST_BODY);
        }

        return $requestBody;
    }
}
