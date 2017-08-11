<?php

namespace App\Controller;

use App\Entity\Diary;
use App\Entity\Food;
use App\Entity\Meal;
use App\Exception\ApiProblemException;
use App\Repository\DiaryRepository;
use Bezhanov\Silex\Routing\Route;
use Hateoas\Representation\CollectionRepresentation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DiaryController extends ResourceController
{
    /**
     * @Route("/diary/{year}/{month}/{day}", methods={"GET"}, requirements={"year": "[\d]{4}", "month": "0[1-9]|1[0-2]", "day": "0[1-9]|[1-2][0-9]|3[0-1]"})
     */
    public function indexAction(Request $request, int $year, int $month, int $day): Response
    {
        /** @var DiaryRepository $repository */
        $repository = $this->em->getRepository($this->getEntityClassName());
        $date = new \DateTime();
        $date->setDate($year, $month, $day);
        $collection = new CollectionRepresentation($repository->findByDate($date));

        return $this->createApiResponse($collection, Response::HTTP_OK);
    }

    /**
     * @Route("/diary/logged-dates/{year}/{month}", methods={"GET"}, requirements={"year": "[\d]{4}", "month": "0[1-9]|1[0-2]"})
     */
    public function loggedDatesAction(Request $request, int $year, int $month): Response
    {
        /** @var DiaryRepository $repository */
        $repository = $this->em->getRepository($this->getEntityClassName());
        $startDate = new \DateTime();
        $startDate->setDate($year, $month, 1);
        $endDate = new \DateTime($startDate->format(\DateTime::RFC3339));
        $endDate->modify('last day of this month');
        $collection = new CollectionRepresentation($repository->findLoggedDatesBetween($startDate, $endDate));

        return $this->createApiResponse($collection, Response::HTTP_OK);
    }

    /**
     * @Route("/diary", methods={"POST"})
     */
    public function createAction(Request $request): Response
    {
        $expectedParameters = ['date', 'meal_id', 'food_id', 'quantity'];
        $requestBody = $this->extractRequestBody($request, $expectedParameters);

        $diary = new Diary();
        $date = new \DateTime($requestBody['date']);
        /** @var Meal $meal */
        $meal = $this->em->getReference(Meal::class, $requestBody['meal_id']);
        /** @var Food $food */
        $food = $this->em->getReference(Food::class, $requestBody['food_id']);
        $diary->setDate($date)->setMeal($meal)->setFood($food)->setQuantity($requestBody['quantity']);
        $violations = $this->validator->validate($diary);

        if ($violations->count() > 0) {
            throw new ApiProblemException(ApiProblemException::TYPE_VALIDATION_ERROR);
        }

        $this->em->persist($diary);
        $this->em->flush();

        $response = $this->createApiResponse('', Response::HTTP_CREATED);
        $response->headers->set('Location', '/diary' . $date->format('/YY/mm/DD'));

        return $response;
    }

    /**
     * @Route("/diary/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, int $id): Response
    {
        $diary = $this->findOrFail($id);
        $this->em->remove($diary);
        $this->em->flush();

        return $this->createApiResponse('', Response::HTTP_NO_CONTENT);
    }

    protected function getEntityClassName(): string
    {
        return Diary::class;
    }
}
