<?php

namespace AppBundle\Repository;
use AppBundle\Entity\User;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectRepository extends \Doctrine\ORM\EntityRepository
{

    public function getUserProjects(User $user){
        return $this->createQueryBuilder('p')
            ->where('p.adminId = :adminId')
            ->setParameter('adminId', $user->getId())
            ->getQuery()
            ->getArrayResult();
    }

    public function getProjectById($id){
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
