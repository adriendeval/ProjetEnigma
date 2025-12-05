<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimelineItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', TextType::class, [
                'label' => 'Date (ex: 1950)',
                'required' => false,
                'attr' => ['class' => 'w-full mb-2 px-3 py-2 border rounded']
            ])
            ->add('text', TextType::class, [
                'label' => 'Événement',
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
