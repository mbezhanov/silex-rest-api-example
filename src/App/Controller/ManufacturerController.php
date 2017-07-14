<?php

namespace App\Controller;

use App\Entity\Manufacturer;
use Bezhanov\Silex\Routing\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
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

    protected function getEntityClassName(): string
    {
        return Manufacturer::class;
    }
}
