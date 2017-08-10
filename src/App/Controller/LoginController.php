<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Exception\ApiProblemException;
use App\Service\JwtService;
use Bezhanov\Silex\Routing\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends BaseController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var JwtService
     */
    private $jwtTokenCreator;

    public function __construct(EntityManagerInterface $em, JwtService $jwtTokenCreator)
    {
        $this->em = $em;
        $this->jwtTokenCreator = $jwtTokenCreator;
    }

    /**
     * @Route("/login", methods={"POST"})
     */
    public function loginAction(Request $request)
    {
        $expectedParameters = ['username', 'password'];
        $requestBody = $this->extractRequestBody($request, $expectedParameters);

        /** @var Profile $profile */
        $profile = $this->em->getRepository(Profile::class)->findOneBy([
            'username' => $requestBody['username']
        ]);

        if (!$profile) {
            throw new ApiProblemException(ApiProblemException::TYPE_INVALID_USERNAME);
        }

        if (!password_verify($requestBody['password'], $profile->getPassword())) {
            throw new ApiProblemException(ApiProblemException::TYPE_INVALID_PASSWORD);
        }

        return $this->createApiResponse(json_encode([
            'authToken' => (string) $this->jwtTokenCreator->createToken($profile->getId())
        ]));
    }

    /**
     * @Route("/login/renew", methods={"POST"})
     */
    public function renewAction(Request $request)
    {
        $expectedParameters = ['token'];
        $requestBody = $this->extractRequestBody($request, $expectedParameters);

        $token = str_replace('Bearer ', '', $requestBody['token']);

        return $this->createApiResponse(json_encode([
            'authToken' => (string) $this->jwtTokenCreator->refreshToken($token)
        ]));
    }
}
