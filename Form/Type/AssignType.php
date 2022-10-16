<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

final class AssignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', HiddenType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('type', HiddenType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Choice([
                        'choices' => ['permission', 'role']
                    ])
                ]
            ])
            ->add('_method', HiddenType::class, [
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'description_permission' => '',
            'exist' => false,
            'disabled' => false
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['checked'] = $options['exist'] ? 'checked' : false;
        $view->vars['disabled'] = $options['disabled'] ? 'disabled' : false;
        $view->vars['description_permission'] = $options['description_permission'];
    }

    public function getBlockPrefix(): string
    {
        return 'owl_rbac_assign';
    }
}
