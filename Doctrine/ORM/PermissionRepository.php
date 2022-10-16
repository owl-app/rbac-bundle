<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Doctrine\ORM;

use Owl\Component\Rbac\Model\AuthItemInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Owl\Component\Rbac\Repository\PermissionRepositoryInterface;

class PermissionRepository extends EntityRepository implements PermissionRepositoryInterface
{
    public function findAllNames(): array
    {
        return $this->createQueryBuilder('o')
            ->select('o.name')
            ->getQuery()
            ->getSingleColumnResult()
        ;
    }

    public function findOneByName(string $name): AuthItemInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
