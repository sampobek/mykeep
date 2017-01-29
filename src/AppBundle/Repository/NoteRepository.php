<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class NoteRepository extends EntityRepository
{
    public function getByTag(User $user, $tag)
    {
        $qb = $this->createQueryBuilder('n');
        $query = $qb
            ->innerJoin('n.tags', 't')
            ->where('t.alias = :tag')
            ->setParameter('tag', $tag)
            ->andWhere('n.user = :user')
            ->setParameter('user', $user)
            ->andWhere('n.isDeleted = :false')
            ->setParameter('false', false)
            ->getQuery();

        $result = $query->getResult();

        return $result;
    }
}
