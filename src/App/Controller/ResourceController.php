<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ResourceController
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

    protected function createApiResponse($data, $statusCode = Response::HTTP_OK): Response
    {
        $data = $this->serializer->serialize($data, 'json');

        return new Response($data, $statusCode, [
            'Content-Type' => 'application/hal+json',
        ]);
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
            throw new \RuntimeException(sprintf('Entity not found: "%s" (id: %s)', $this->getEntityClassName(), $id));
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
