<?php

namespace App\Controller;

use App\Entity\Food;
use App\Entity\Manufacturer;
use App\Exception\ApiProblemException;
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
        $collection = $factory->createRepresentation($pager, new \Hateoas\Configuration\Route('list_foods'));

        return $this->createApiResponse($collection, Response::HTTP_OK);
    }

    /**
     * @Route("/foods", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $expectedParameters = ['name', 'servingSize', 'calories', 'carbs', 'fat', 'protein', 'manufacturer_id'];
        $requestBody = $this->extractRequestBody($request, $expectedParameters);
        $requestBody['manufacturer'] = $this->em->getReference(Manufacturer::class, $requestBody['manufacturer_id']);
        array_splice($expectedParameters, -1, 1, ['manufacturer']);

        $food = new Food();
        $food->fromArray($requestBody, $expectedParameters);
        $violations = $this->validator->validate($food);

        if ($violations->count() > 0) {
            throw new ApiProblemException(ApiProblemException::TYPE_VALIDATION_ERROR);
        }
        $this->em->persist($food);
        $this->em->flush();

        return $this->createApiResponse('', Response::HTTP_CREATED, [
            'Location' => sprintf('/foods/%d', $food->getId())
        ]);
    }

    /**
     * @Route("/foods/{id}", methods={"GET"})
     */
    public function readAction($id)
    {
        $food = $this->findOrFail($id);

        return $this->createApiResponse($food, Response::HTTP_OK);
    }

    /**
     * @Route("/foods/{id}", methods={"PUT", "PATCH"}, requirements={"id": "\d+"})
     */
    public function updateAction(Request $request, int $id): Response
    {
        $expectedParameters = ['servingSize', 'calories', 'carbs', 'fat', 'protein', 'manufacturer_id'];
        $requestBody = $this->extractRequestBody($request, $expectedParameters);
        $requestBody['manufacturer'] = $this->em->getReference(Manufacturer::class, $requestBody['manufacturer_id']);
        array_splice($expectedParameters, -1, 1, ['manufacturer']);
        $food = $this->findOrFail($id);
        $food->fromArray($requestBody, $expectedParameters);
        $violations = $this->validator->validate($food);

        if ($violations->count() > 0) {
            throw new ApiProblemException(ApiProblemException::TYPE_VALIDATION_ERROR);
        }
        $this->em->flush();

        return $this->createApiResponse(null, Response::HTTP_NO_CONTENT);
    }

    protected function getEntityClassName(): string
    {
        return Food::class;
    }
}
