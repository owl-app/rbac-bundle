<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Doctrine\ORM;

use Owl\Component\Rbac\Model\RoleInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Owl\Component\Rbac\Repository\RoleRepositoryInterface;

/**
 * @template T of RoleInterface
 *
 * @implements RoleRepositoryInterface<T>
 */
class RoleRepository extends EntityRepository implements RoleRepositoryInterface
{
}
