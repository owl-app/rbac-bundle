<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class PermissionType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', HiddenType::class)
            ->add('group_permission', HiddenType::class)
            ->add('description', HiddenType::class);
        ;
    }

    /**
     * @return string
     *
     * @psalm-return 'owl_rbac_permission'
     */
    public function getBlockPrefix(): string
    {
        return 'owl_rbac_permission';
    }
}
