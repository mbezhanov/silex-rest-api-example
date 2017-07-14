<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Representation\ApiProblemRepresentation;
use Bezhanov\Silex\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends ResourceController
{
    private const THE_ONLY_USER_ID_CURRENTLY_PRESENT = 1;

    /**
     * @Route("/profile", methods={"POST", "OPTIONS"})
     */
    public function updateAction(Request $request): Response
    {
        // @todo: perform authentication check
        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return $this->createApiResponse(null, 200, [
                'Access-Control-Allow-Methods' => ['POST'],
                'Access-Control-Allow-Headers' => 'Content-Type',
            ]);
        }

        // @todo: validate request body!
        $requestBody = json_decode($request->getContent(), true);
        $profile = $this->findOrFail(self::THE_ONLY_USER_ID_CURRENTLY_PRESENT);

        if (!password_verify($requestBody['old_password'], $profile->getPassword())) {
            return $this->createApiProblem(ApiProblemRepresentation::TYPE_INVALID_PASSWORD);
        }
        $newPassword = !empty($requestBody['new_password']) ? password_hash($requestBody['new_password'], PASSWORD_DEFAULT) : '';
        $profile->setPassword($newPassword);
        $violations = $this->validator->validate($profile);

        if ($violations->count() > 0) {
            return $this->createApiProblem(ApiProblemRepresentation::TYPE_VALIDATION_ERROR);
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
