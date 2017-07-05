<?php

namespace App\Controller;

use App\Entity\Manufacturer;
use Bezhanov\Silex\Routing\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManufacturerController extends ResourceController
{
    /**
     * @Route("/manufacturers", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
        $collection = $this->em->getRepository($this->getEntityClassName())->findAll();
        $data = $this->serializer->serialize($collection, 'hal+json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/manufacturers", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $item = new Manufacturer();
        $item->setName($request->request->get('name'));

        $this->em->persist($item);
        $this->em->flush();

        $response = new Response('', Response::HTTP_CREATED);
        $response->headers->set('Location', sprintf('/manufacturers/%d', $item->getId()));

        return $response;
    }

    /**
     * @Route("/manufacturers/{id}", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function readAction(int $id)
    {
        $entity = $this->findOrFail($id);
        $data = $this->serializer->serialize($entity, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/manufacturers/{id}", methods={"PUT", "PATCH"}, requirements={"id": "\d+"})
     */
    public function updateAction(int $id, Request $request)
    {
        $requestBody = json_decode($request->getContent(), true);
        $entity = $this->findOrFail($id);
        $entity->fromArray($requestBody, ['name']);

        $errors = $this->validator->validate($entity);

        if (count($errors) > 0) {
            die('validation failed: ' . PHP_EOL . $errors);
        }

        $this->em->persist($entity);
        $this->em->flush();

        $response = new Response('', Response::HTTP_NO_CONTENT);

        return $response;
    }

    /**
     * @Route("/manufacturers/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     */
    public function deleteAction(int $id)
    {
        $entity = $this->findOrFail($id);
        $this->em->remove($entity);
        $this->em->flush();

        $response = new Response('', Response::HTTP_NO_CONTENT);

        return $response;
    }

    protected function getEntityClassName(): string
    {
        return Manufacturer::class;
    }
}
