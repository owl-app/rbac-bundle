<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Controller;

use Owl\Bridge\SyliusResource\Controller\BaseController;
use Owl\Bundle\RbacBundle\Factory\PermissionFormFactoryInterface;
use Owl\Component\Rbac\Model\PermissionInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Exception\DeleteHandlingException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class PermissionController extends BaseController
{
    public function availablesAction(Request $request, PermissionFormFactoryInterface $permissionFormFactory): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, 'availables_permission');

        return $this->render($configuration->getTemplate('index.html'), [
            'configuration' => $configuration,
            'metadata' => $this->metadata,
            'forms' => $permissionFormFactory->createByRoutes($configuration),
        ]);
    }

    public function addAction(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'It should be XHR request');
        }

        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $formOptions = array_merge(
            $configuration->getFormOptions(),
            [
                'csrf_field_name' => '_csrf_token',
                'csrf_token_id' => $request->request->get('name'),
            ],
        );

        $this->isGrantedOr403($configuration, 'add_availables_permission');
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);

        $form = $this->container->get('form.factory')->createNamed('', $configuration->getFormType(), $newResource, $formOptions);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $newResource = $form->getData();

            $this->repository->add($newResource);

            $responseData = [
                'message' => $this->get('translator')->trans('owl.rbac.permission.add_success', [], 'flashes'),
                'data' => $newResource,
            ];

            return $this->createRestView($configuration, $responseData, Response::HTTP_CREATED);
        }

        return new JsonResponse([
            'status' => 'error',
            'errors' => $this->getErrorMessages($form),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function removeAction(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'It should be XHR request');
        }

        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, 'remove');
        /** @var PermissionInterface $resource */
        $resource = $this->findOr404($configuration);

        $this->validateCsrfProtection($request, $configuration, $resource->getName());

        try {
            $this->resourceDeleteHandler->handle($resource, $this->repository);
        } catch (DeleteHandlingException $exception) {
            return $this->createRestView($configuration, null, $exception->getApiResponseCode());
        }

        $responseData = [
            'message' => $this->get('translator')->trans('owl.rbac.permission.remove_success', [], 'flashes'),
        ];

        return $this->createRestView($configuration, $responseData, Response::HTTP_OK);
    }

    private function validateCsrfProtection(Request $request, RequestConfiguration $configuration, string $name): void
    {
        if ($configuration->isCsrfProtectionEnabled() && !$this->isCsrfTokenValid($name, (string) $request->request->get('_csrf_token'))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }
    }
}
