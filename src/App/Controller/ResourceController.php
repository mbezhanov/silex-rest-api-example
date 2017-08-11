<?php

namespace App\Controller;

use App\Exception\ApiProblemException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ResourceController extends BaseController
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    protected function createApiResponse($data = null, $statusCode = Response::HTTP_OK, $headers = []): Response
    {
        if (!is_null($data)) {
            $data = $this->serializer->serialize($data, 'json');
        }

        return parent::createApiResponse($data, $statusCode, $headers);
    }

    /**
     * Finds an Entity by its PK or throws an Exception upon failure
     *
     * @param int $id
     * @return object
     * @throws \RuntimeException
     */
    protected function findOrFail(int $id)
    {
        $entity = $this->em->find($this->getEntityClassName(), $id);

        if (!$entity) {
            $message = sprintf('Entity not found: "%s" (id: %s)', $this->getEntityClassName(), $id);
            throw new ApiProblemException(ApiProblemException::TYPE_ENTITY_NOT_FOUND, Response::HTTP_BAD_REQUEST, $message);
        }

        return $entity;
    }

    /**
     * Returns the class name of the Entity that the Controller is primarily responsible for.
     *
     * @return string
     */
    abstract protected function getEntityClassName(): string;
}
