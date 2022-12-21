<?php

declare(strict_types=1);

namespace HetBonteHert\Module\MenuCard\Form;

use HetBonteHert\Module\MenuCard\Entity\MenuCard;
use HetBonteHert\Module\MenuCard\Entity\MenuPage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tidi\Cms\Module\Core\Form\StructurePropertiesType;

final class MenuPageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class, [
                'attr' => [
                    'class' => 'mce_edit',
                ],
                'required' => false,
            ])
            ->add('structure', StructurePropertiesType::class, [
                'label' => false,
            ])
            ->add('menuCard', EntityType::class, [
                'class'    => MenuCard::class,
                'required' => true,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MenuPage::class,
        ]);
    }
}
