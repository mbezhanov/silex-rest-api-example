<?php

namespace App\Representation;

use Symfony\Component\HttpFoundation\Response;

/**
 * A wrapper for holding data to be used for a application/problem+json response
 */
class ApiProblemRepresentation
{
    const TYPE_INVALID_PASSWORD = 'invalid_password';
    const TYPE_PASSWORD_UPDATE_DISABLED = 'password_update_disabled';
    const TYPE_VALIDATION_ERROR = 'validation_error';

    private static $titles = [
        self::TYPE_INVALID_PASSWORD => 'The supplied password is not valid.',
        self::TYPE_PASSWORD_UPDATE_DISABLED => 'Updating the test account password is disabled in this demo.',
        self::TYPE_VALIDATION_ERROR => 'Failed validating the submitted data.',
    ];

    private $type;

    private $title;

    private $statusCode;

    private $extraData = [];

    public function __construct($type = null, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $this->statusCode = $statusCode;

        if (is_null($type)) {
            $type = 'about:blank';
            $title = isset(Response::$statusTexts[$statusCode])
                ? Response::$statusTexts[$statusCode]
                : 'Unknown status code';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException('No title for type ' . $type);
            }
            $title = self::$titles[$type];
        }

        $this->type = $type;
        $this->title = $title;
    }

    public function toArray()
    {
        return array_merge(
            $this->extraData,
            [
                'status' => $this->statusCode,
                'type' => $this->type,
                'title' => $this->title,
            ]
        );
    }

    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
