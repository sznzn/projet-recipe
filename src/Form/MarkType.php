<?php

namespace App\Form;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mark', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ],
            'attr' => [
                'class' => 'form-select w-75'
            ],
            'label' => 'Noter la recette',
            'label_attr' => ['class' => 'form-label mt-2']
        ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary my-2'
                ],
                'label' => 'Note la recette'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mark::class,
        ]);
    }
}
