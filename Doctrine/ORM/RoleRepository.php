<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Doctrine\ORM;

use Owl\Component\Rbac\Model\RoleInterface;
use Owl\Component\Rbac\Repository\RoleRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of RoleInterface
 *
 * @implements RoleRepositoryInterface<T>
 */
class RoleRepository extends EntityRepository implements RoleRepositoryInterface
{
}
