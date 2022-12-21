<?php

namespace HetBonteHert\Module\MenuCard\Form;

use HetBonteHert\Module\MenuCard\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'         => 'category.name',
                'required'      => true,
            ])
            ->add('active', CheckboxType::class, [
                'label'         => 'category.active',
                'required'      => false,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addTextFields']);
    }

    /**
     * @param FormEvent $event
     */
    public function addTextFields(FormEvent $event): void
    {
        $category = $event->getData();
        $form     = $event->getForm();

        if (!$category instanceof Category) {
            return;
        }

        if (null === $category->getId()) {
            $form
                ->add('description', TextareaType::class, [
                    'label'         => 'category.description',
                    'attr'          => ['class' => 'mce_edit_basic', 'data-theme' => 'advanced'],
                    'required'      => false,
                ]);
        } else {
            $form
                ->add('description', TextareaType::class, [
                    'label'         => 'category.description',
                    'attr'          => ['class' => 'mce_edit', 'data-theme' => 'advanced'],
                    'required'      => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
