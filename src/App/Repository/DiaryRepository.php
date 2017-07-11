<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class DiaryRepository extends EntityRepository
{
    public function findByDate(\DateTime $date)
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->innerJoin('d.meal', 'm')
            ->andWhere('d.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->addOrderBy('d.date', 'ASC')
            ->addOrderBy('m.id', 'ASC');

        return $queryBuilder->getQuery()->execute();
    }


    public function findLoggedDatesBetween(\DateTime $startDate, \DateTime $endDate)
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->select('DISTINCT d.date')
            ->andWhere('d.date BETWEEN :from AND :to')
            ->setParameter('from', $startDate->format('Y-m-d'))
            ->setParameter('to', $endDate->format('Y-m-d'))
            ->addOrderBy('d.date', 'ASC');

        return $queryBuilder->getQuery()->execute();
    }
}
