<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Controller;

use Exception;
use Owl\Bundle\RbacBundle\Factory\PermissionFormFactoryInterface;
use Owl\Bundle\RbacManagerBundle\Factory\ItemFactoryInterface;
use Owl\Bridge\SyliusResource\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Owl\Bundle\RbacManagerBundle\Manager\ManagerInterface;
use Owl\Bundle\RbacManagerBundle\Types\Item;

final class RoleController extends BaseController
{
    public function availablesAction(Request $request, PermissionFormFactoryInterface $permissionFormFactory, ManagerInterface $rbacManager): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $resource = $this->findOr404($configuration);

        $this->isGrantedOr403($configuration, 'role_availabes', $resource);

        $forms = $permissionFormFactory->createByExists(
            $configuration,
            array_keys($rbacManager->getPermissionsByRole($resource->getName()))
        );

        return $this->render($configuration->getTemplate('index.html'), [
            'configuration' => $configuration,
            'metadata' => $this->metadata,
            'role' => $resource,
            'forms' => $forms
        ]);
    }

    public function assignAction(Request $request, ManagerInterface $rbacManager, ItemFactoryInterface $rbacItemFactory): Response
    {
        return $this->changePermission('add', $request, $rbacManager, $rbacItemFactory);
    }

    public function revokeAction(Request $request, ManagerInterface $rbacManager, ItemFactoryInterface $rbacItemFactory): Response
    {
        return $this->changePermission('remove', $request, $rbacManager, $rbacItemFactory);
    }

    /**
     * @return Response|null
     */
    private function changePermission(string $action, Request $request, ManagerInterface $rbacManager, ItemFactoryInterface $rbacItemFactory)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $formOptions = array_merge(
            $configuration->getFormOptions(),
            [
                'csrf_field_name' => '_csrf_token',
                'csrf_token_id' => $request->get('name')
            ]
        );
        $method = $action === 'remove' ? 'DELETE' : 'POST';

        $this->isGrantedOr403($configuration, 'role_'.$action);
        $role = $this->findOr404($configuration);

        $form = $this->container->get('form.factory')->createNamed('', $configuration->getFormType(), null, $formOptions);
        $form->handleRequest($request);

        if ($request->isMethod($method) && $form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $roleItem = $rbacItemFactory->create(Item::TYPE_ROLE, $role->getName());
            $assignItem = $rbacItemFactory->create($formData['type'], $formData['name']);

            try {
                $rbacManager->{$action.'Child'}($roleItem, $assignItem);

                if (!$configuration->isHtmlRequest()) {
                    $responseData = [
                        'message' => $this->get('translator')->trans('owl.rbac.permission.add_success', [], 'flashes')
                    ];
                    return $this->createRestView($configuration, $responseData, Response::HTTP_OK);
                }
            } catch(Exception $e) {
                $responseData = [
                    'message' => $e->getMessage()
                ];
                return $this->createRestView($configuration, $responseData, Response::HTTP_BAD_REQUEST);
            }
        } else {
            $responseData = [
                'message' => [
                    'status' => 'error',
                    'errors' => $this->getErrorMessages($form)
                ]
            ];
            return $this->createRestView($configuration, $responseData, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
