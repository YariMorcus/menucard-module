<?php

namespace HetBonteHert\Module\MenuCard\Form;

use HetBonteHert\Module\MenuCard\Entity\Category;
use HetBonteHert\Module\MenuCard\Entity\Dish;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tidi\Bundle\MoneyBundle\Form\Type\MoneyType;

class DishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'         => 'menu_card.name',
                'required'      => true,
            ])
            ->add('category', EntityType::class, [
                'label'         => 'menu_card.category',
                'class'         => Category::class,
                'required'      => true,
                'placeholder'   => 'menu_card.category_placeholder',
            ])
            ->add('price', MoneyType::class, [
                'label'     => 'dish.price',
            ])
            ->add('active', CheckboxType::class, [
                'label'         => 'dish.active',
                'required'      => false,
            ])
            ->add('highlighted', CheckboxType::class, [
                'label'         => 'dish.highlighted.name',
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
        $dish = $event->getData();
        $form = $event->getForm();

        if (!$dish instanceof Dish) {
            return;
        }

        if (null === $dish->getId()) {
            $form
                ->add('description', TextareaType::class, [
                    'label'         => 'menu_card.description',
                    'attr'          => ['class' => 'mce_edit_basic', 'data-theme' => 'advanced'],
                    'required'      => false,
                ]);
        } else {
            $form
                ->add('description', TextareaType::class, [
                    'label'         => 'menu_card.description',
                    'attr'          => ['class' => 'mce_edit', 'data-theme' => 'advanced'],
                    'required'      => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dish::class,
        ]);
    }
}
