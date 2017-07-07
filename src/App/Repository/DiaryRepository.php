<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class DiaryRepository extends EntityRepository
{
    public function findByDateRange(\DateTime $startDate, \DateTime $endDate) {
        $queryBuilder = $this->createQueryBuilder('d')
            ->innerJoin('d.meal', 'm')
            ->andWhere('d.date BETWEEN :from AND :to')
            ->setParameter('from', $startDate)
            ->setParameter('to', $endDate)
            ->addOrderBy('d.date', 'ASC')
            ->addOrderBy('m.id', 'ASC');

        return $queryBuilder->getQuery()->execute();
    }
}
