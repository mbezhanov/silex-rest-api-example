<?php

namespace App\Controller;

use App\Entity\Manufacturer;
use Bezhanov\Silex\Routing\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManufacturerController extends ResourceController
{
    /**
     * @Route("/manufacturers", methods={"GET"}, name="list_manufacturers")
     */
    public function indexAction(Request $request): Response
    {
        $queryBuilder = $this->em->createQueryBuilder()->select('m')->from($this->getEntityClassName(), 'm')->addOrderBy('m.name', 'ASC');
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($adapter);
        $pager->setCurrentPage($request->query->get('page', 1))->setMaxPerPage($request->query->get('limit', 10));
        $factory = new PagerfantaFactory();
        $collection = $factory->createRepresentation($pager, new \Hateoas\Configuration\Route('list_manufacturers'));

        return $this->createApiResponse($collection, Response::HTTP_OK);
    }

    /**
     * @Route("/manufacturers", methods={"POST"})
     */
    public function createAction(Request $request): Response
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
    public function readAction(int $id): Response
    {
        $entity = $this->findOrFail($id);
        $data = $this->serializer->serialize($entity, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/manufacturers/{id}", methods={"PUT", "PATCH"}, requirements={"id": "\d+"})
     */
    public function updateAction(int $id, Request $request): Response
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
    public function deleteAction(int $id): Response
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
