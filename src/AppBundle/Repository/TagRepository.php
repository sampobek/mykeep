<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
    public function getTags()
    {
        $qb = $this->createQueryBuilder('t');
        $query = $qb
            ->innerJoin('t.notes', 'n')
            ->where('n.isDeleted = :false')
            ->setParameter('false', false)
            ->getQuery();

        $result = $query->getResult();

        return $result;
    }
}
