<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Factory;

use Owl\Component\Rbac\Factory\PermissionFactoryInterface;
use Owl\Component\Rbac\Model\AuthItemInterface;
use Owl\Component\Rbac\Provider\RoutesPermissionProviderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Symfony\Component\Form\FormFactoryInterface;
use Owl\Component\Rbac\Repository\PermissionRepositoryInterface;
use Owl\Component\Rbac\Repository\RoleRepositoryInterface;

final class PermissionFormFactory implements PermissionFormFactoryInterface
{
    public function __construct(
        private PermissionFactoryInterface $permissionFactory,
        private PermissionRepositoryInterface $permissionRepository,
        private RoleRepositoryInterface $roleRepository,
        private RoutesPermissionProviderInterface $routesPermissionProvider,
        private FormFactoryInterface $formFactory
    ) {

    }

    public function createByRoutes(RequestConfiguration $requestConfiguration): array
    {
        $formsPermission = [];
        $routes = $this->routesPermissionProvider->getPermissions();
        $existPermissions = $this->permissionRepository->findAllNames();

        foreach($routes as $name => $route) {
            $formsPermission[$route['group']][] = $this->createForm(
                $this->permissionFactory->createWithData($name, $route['group'], $route['description']), 
                $requestConfiguration,
                in_array($name, $existPermissions)
            );
        }

        return $formsPermission;
    }

    public function createByExists(RequestConfiguration $requestConfiguration, array $assignedPermissions, array $disabledPermissions = [], bool $withRoles = false): array
    {
        $formsPermission = [];
        $existPermissions = $this->permissionRepository->findAll();

        if($withRoles) {
            $existsRoles = $this->roleRepository->findAll();

            foreach($existsRoles as $role) {
                $formsPermission['availables_roles'][] = $this->createForm(
                    $role, 
                    $requestConfiguration,
                    in_array($role->getName(), $assignedPermissions),
                );
            }
        }
        
        foreach($existPermissions as $permission) {
            $formsPermission[$permission->getGroupPermission()][] = $this->createForm(
                $permission, 
                $requestConfiguration,
                in_array($permission->getName(), $assignedPermissions),
                ['disabled' => in_array($permission->getName(), $disabledPermissions)]
            );
        }

        return $formsPermission;
    }

    private function createForm(AuthItemInterface $permission, RequestConfiguration $requestConfiguration, bool $exist, array $customFormOptions = [])
    {
        $formOptions = array_merge($requestConfiguration->getFormOptions(), $customFormOptions, [
            'description_permission' => $permission->getDescription(),
            'exist' => $exist,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => $permission->getName()
        ]);

        $form = $this->formFactory->createNamed('', $requestConfiguration->getFormType(), $permission, $formOptions);

        return $form->createView();
    }

    private function getDataFromRoute(string $name, Route $route): array
    {
        $vars = $route->getDefaults()['_sylius']['vars'] ?? [];
        $group = $vars['permission']['group'] ?? false;
        $description = $vars['permission']['description'] ?? 'owl.ui.permission.'.$name;

        return [$group, $description];
    }
}
