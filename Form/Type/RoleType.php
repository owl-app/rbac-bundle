<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class RoleType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'owl.form.common.name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'owl.form.common.description',
                'required' => false
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'owl_rbac_role';
    }
}
