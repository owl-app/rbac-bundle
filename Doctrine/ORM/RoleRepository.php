<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Owl\Component\Rbac\Repository\RoleRepositoryInterface;

class RoleRepository extends EntityRepository implements RoleRepositoryInterface
{
}
