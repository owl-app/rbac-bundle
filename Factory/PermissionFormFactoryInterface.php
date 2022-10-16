<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Factory;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;

interface PermissionFormFactoryInterface
{
    public function createByRoutes(RequestConfiguration $requestConfiguration): array;

    public function createByExists(RequestConfiguration $requestConfiguration, array $assignedPermissions, array $disabledPermissions = [], bool $withRoles = false): array;
}
