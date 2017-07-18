<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Representation\ApiProblemRepresentation;
use Bezhanov\Silex\Routing\Route;
use Lcobucci\JWT\Parser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends ResourceController
{
    /**
     * @var Parser
     */
    private $jwt;

    public function setJwtParser(Parser $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * @Route("/profile", methods={"POST"})
     */
    public function updateAction(Request $request): Response
    {
        // @todo: validate request body!
        $requestBody = json_decode($request->getContent(), true);

        list($bearer, $token) = explode(' ', $request->headers->get('Authorization'));

        $token = $this->jwt->parse($token);
        $profile = $this->findOrFail($token->getClaim('uid'));

        if (!password_verify($requestBody['old_password'], $profile->getPassword())) {
            return $this->createApiProblem(ApiProblemRepresentation::TYPE_INVALID_PASSWORD);
        }
        $newPassword = !empty($requestBody['new_password']) ? password_hash($requestBody['new_password'], PASSWORD_DEFAULT) : '';
        $profile->setPassword($newPassword);
        $violations = $this->validator->validate($profile);

        if ($violations->count() > 0) {
            return $this->createApiProblem(ApiProblemRepresentation::TYPE_VALIDATION_ERROR);
        }

        // prevent users from changing the default username and password on the demo installation
        if (getenv('DISABLE_PASSWORD_UPDATE')) {
            return $this->createApiProblem(ApiProblemRepresentation::TYPE_PASSWORD_UPDATE_DISABLED);
        }

        $this->em->persist($profile);
        $this->em->flush();

        return $this->createApiResponse(null, Response::HTTP_NO_CONTENT);
    }


    protected function getEntityClassName(): string
    {
        return Profile::class;
    }
}
