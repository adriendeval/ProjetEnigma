<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class McqQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextType::class, [
                'label' => 'Question',
                'attr' => ['class' => 'w-full mb-2 px-3 py-2 border rounded']
            ])
            ->add('answer0', TextType::class, [
                'label' => 'Réponse A',
                'attr' => ['class' => 'w-full mb-1 px-3 py-2 border rounded']
            ])
            ->add('answer1', TextType::class, [
                'label' => 'Réponse B',
                'attr' => ['class' => 'w-full mb-1 px-3 py-2 border rounded']
            ])
            ->add('answer2', TextType::class, [
                'label' => 'Réponse C',
                'attr' => ['class' => 'w-full mb-1 px-3 py-2 border rounded']
            ])
            ->add('answer3', TextType::class, [
                'label' => 'Réponse D',
                'attr' => ['class' => 'w-full mb-1 px-3 py-2 border rounded']
            ])
            ->add('correct', ChoiceType::class, [
                'label' => 'Bonne réponse',
                'choices' => [
                    'Réponse A' => 0,
                    'Réponse B' => 1,
                    'Réponse C' => 2,
                    'Réponse D' => 3,
                ],
                'attr' => ['class' => 'w-full mb-2 px-3 py-2 border rounded']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
