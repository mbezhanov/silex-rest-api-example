<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Representation\ApiProblemRepresentation;
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
        // @todo: validate request body!
        $requestBody = json_decode($request->getContent(), true);

        /** @var Profile $profile */
        $profile = $this->em->getRepository(Profile::class)->findOneBy([
            'username' => $requestBody['username']
        ]);

        if (!$profile) {
            // @todo: throw api problem exception
        }

        if (!password_verify($requestBody['password'], $profile->getPassword())) {
            return $this->createApiProblem(ApiProblemRepresentation::TYPE_INVALID_PASSWORD);
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
        // @todo: validate request body!
        $requestBody = json_decode($request->getContent(), true);

        $token = str_replace('Bearer ', '', $requestBody['token']);

        return $this->createApiResponse(json_encode([
            'authToken' => (string) $this->jwtTokenCreator->refreshToken($token)
        ]));
    }
}
