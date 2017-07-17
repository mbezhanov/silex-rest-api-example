<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Representation\ApiProblemRepresentation;
use Bezhanov\Silex\Routing\Route;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Builder;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends BaseController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Builder
     */
    private $jwt;

    public function __construct(EntityManagerInterface $em, Builder $jwt)
    {
        $this->em = $em;
        $this->jwt = $jwt;
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

        $authToken = $this->jwt->setId(uniqid(), true)
            ->setIssuedAt(time())
            ->setNotBefore(time() + 60)
            ->setExpiration(time() + 3600)
            ->set('uid', $profile->getId())
            ->getToken();

        return $this->createApiResponse(json_encode(['authToken' => (string)$authToken]));
    }
}
