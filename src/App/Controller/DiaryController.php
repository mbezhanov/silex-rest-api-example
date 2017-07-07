<?php

namespace App\Controller;

use App\Entity\Diary;
use App\Repository\DiaryRepository;
use Bezhanov\Silex\Routing\Route;
use Hateoas\Representation\CollectionRepresentation;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/diary")
 */
class DiaryController extends ResourceController
{
    /**
     * @Route("/{year}/{month}", methods={"GET"}, requirements={"year": "[\d]{4}", "month": "0[1-9]|1[0-2]"})
     */
    public function indexAction(int $year, int $month)
    {
        /** @var DiaryRepository $repository */
        $repository = $this->em->getRepository($this->getEntityClassName());
        $startDate = new \DateTime();
        $startDate->setDate($year, $month, 1);
        $endDate = new \DateTime($startDate->format(\DateTime::RFC3339));
        $endDate->modify('last day of this month');
        $collection = new CollectionRepresentation($repository->findByDateRange($startDate, $endDate));
        return $this->createApiResponse($collection, Response::HTTP_OK);
    }

    protected function getEntityClassName(): string
    {
        return Diary::class;
    }
}
