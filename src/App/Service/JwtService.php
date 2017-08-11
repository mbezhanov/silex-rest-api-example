<?php

namespace App\Service;

use App\Exception\ApiProblemException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

class JwtService
{
    /**
     * @var Builder
     */
    private $jwt;
    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var Signer
     */
    private $signer;
    /**
     * @var string
     */
    private $apiTokenSignKey;

    public function __construct(Builder $jwt, Parser $parser, Signer $signer, string $apiTokenSignKey)
    {
        $this->jwt = $jwt;
        $this->parser = $parser;
        $this->signer = $signer;
        $this->apiTokenSignKey = $apiTokenSignKey;
    }

    public function createToken(int $userId): Token
    {
        return $this->jwt->setId(uniqid(), true)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration(time() + 3600)
            ->set('uid', $userId)
            ->sign($this->signer, $this->apiTokenSignKey)
            ->getToken();
    }

    public function refreshToken(string $previousToken): Token
    {
        $this->validateToken($previousToken);
        $previousToken = $this->parser->parse($previousToken);

        return $this->createToken($previousToken->getClaim('uid'));
    }

    public function validateToken(string $token): bool
    {
        $data = new ValidationData();

        try {
            $token = $this->parser->parse($token);
        } catch (\Exception $e) {
            throw new ApiProblemException(ApiProblemException::TYPE_INVALID_TOKEN);
        }

        return $token->validate($data);
    }
}
