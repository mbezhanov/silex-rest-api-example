<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * A wrapper for holding data to be used for a application/problem+json response
 */
class ApiProblemException extends HttpException
{
    const TYPE_ENTITY_NOT_FOUND = 'entity_not_found';
    const TYPE_INVALID_PASSWORD = 'invalid_password';
    const TYPE_INVALID_REQUEST_BODY = 'invalid_request_body';
    const TYPE_INVALID_TOKEN = 'invalid_token';
    const TYPE_INVALID_USERNAME = 'invalid_username';
    const TYPE_PASSWORD_UPDATE_DISABLED = 'password_update_disabled';
    const TYPE_VALIDATION_ERROR = 'validation_error';

    private static $titles = [
        self::TYPE_ENTITY_NOT_FOUND => 'Entity not found',
        self::TYPE_INVALID_PASSWORD => 'The supplied password is not valid.',
        self::TYPE_INVALID_REQUEST_BODY => 'The request body is not valid.',
        self::TYPE_INVALID_TOKEN => 'The supplied auth token is not valid.',
        self::TYPE_INVALID_USERNAME => 'The supplied username is not valid.',
        self::TYPE_PASSWORD_UPDATE_DISABLED => 'Updating the test account password is disabled in this demo.',
        self::TYPE_VALIDATION_ERROR => 'Failed validating the submitted data.',
    ];

    private $type;

    private $title;

    private $extraData = [];

    public function __construct($type = null, $statusCode = Response::HTTP_BAD_REQUEST, $message = null, \Exception $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);

        if (is_null($type)) {
            $type = 'about:blank';
            $title = isset(Response::$statusTexts[$statusCode])
                ? Response::$statusTexts[$statusCode]
                : 'Unknown status code';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException('No title for type ' . $type);
            }
            $title = $this->getMessage() ?: self::$titles[$type];
        }

        $this->type = $type;
        $this->title = $title;
    }

    public function toArray()
    {
        return array_merge(
            $this->extraData,
            [
                'status' => $this->getStatusCode(),
                'type' => $this->type,
                'title' => $this->title,
            ]
        );
    }

    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }
}
