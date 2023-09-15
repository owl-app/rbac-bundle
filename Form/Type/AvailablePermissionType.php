<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AvailablePermissionType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', HiddenType::class)
            ->add('group_permission', HiddenType::class)
            ->add('description', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'description_permission' => '',
            'exist' => false,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['checked'] = $options['exist'] ? 'checked' : false;
        $view->vars['description_permission'] = $options['description_permission'];
    }

    /**
     * @psalm-return 'owl_rbac_permission_available'
     */
    public function getBlockPrefix(): string
    {
        return 'owl_rbac_permission_available';
    }
}
