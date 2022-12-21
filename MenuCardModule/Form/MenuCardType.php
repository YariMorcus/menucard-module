<?php

namespace HetBonteHert\Module\MenuCard\Form;

use HetBonteHert\Module\MenuCard\Entity\Dish;
use HetBonteHert\Module\MenuCard\Entity\MenuCard;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', textType::class, [
                'label'         => 'menu_card.name',
                'required'      => true,
            ])
            ->add('dishes', EntityType::class, [
                'label'         => 'menu_card.dish',
                'class'         => Dish::class,
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
                'attr'          => ['class' => 'js-select2'],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addTextFields']);
    }

    /**
     * @param FormEvent $event
     */
    public function addTextFields(FormEvent $event): void
    {
        $menuCard = $event->getData();
        $form     = $event->getForm();

        if (!$menuCard instanceof MenuCard) {
            return;
        }

        if (null === $menuCard->getId()) {
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
            'data_class' => MenuCard::class,
        ]);
    }
}
