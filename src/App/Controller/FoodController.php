<?php

namespace App\Controller;

use App\Entity\Food;
use Bezhanov\Silex\Routing\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FoodController extends ResourceController
{
    /**
     * @Route("/foods", methods={"GET"}, name="list_foods")
     */
    public function indexAction(Request $request): Response
    {
        $queryBuilder = $this->em->createQueryBuilder()->select('f')->from($this->getEntityClassName(), 'f')->addOrderBy('f.name');
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($adapter);
        $pager->setCurrentPage($request->query->get('page', 1))->setMaxPerPage($request->query->get('limit', 10));
        $factory = new PagerfantaFactory();
        $collection = $factory->createRepresentation($pager, new \Hateoas\Configuration\Route('list_manufacturers'));

        return $this->createApiResponse($collection, Response::HTTP_OK);
    }

    /**
     * @Route("/foods", methods={"POST"})
     */
    public function createAction()
    {
        return 'store new food';
    }

    /**
     * @Route("/foods/{id}", methods={"GET"})
     */
    public function readAction($id)
    {
        return "read $id";
    }

    /**
     * @Route("/foods/{id}", methods={"PUT", "PATCH"})
     */
    public function updateAction($id)
    {
        return "update $id";
    }

    /**
     * @Route("/foods/{id}", methods={"DELETE"})
     */
    public function deleteAction($id)
    {
        return "delete $id";
    }

    protected function getEntityClassName(): string
    {
        return Food::class;
    }


}
