<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
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

    /**
     * @return QueryBuilder
     */
    public function findOneByName(array ... $name): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.name = :name')
            ->setParameter('name', $name)
        ;
    }
}
