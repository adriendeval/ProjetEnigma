<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PhotoPairType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image1', TextType::class, [
                'label' => '🖼️ Image 1',
                'help' => 'Nom du fichier (ex: photo1.jpg) - doit être dans public/images/enigmas/',
                'attr' => [
                    'class' => 'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500',
                    'placeholder' => 'photo1.jpg'
                ]
            ])
            ->add('image2', TextType::class, [
                'label' => '🖼️ Image 2',
                'help' => 'Nom du fichier (ex: photo2.jpg)',
                'attr' => [
                    'class' => 'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500',
                    'placeholder' => 'photo2.jpg'
                ]
            ])
            ->add('correct', ChoiceType::class, [
                'label' => '✅ Quelle image est la VRAIE photo ? (non générée par IA)',
                'choices' => [
                    'Image 1 est la vraie photo' => 0,
                    'Image 2 est la vraie photo' => 1,
                ],
                'expanded' => true,
                'attr' => ['class' => 'flex gap-6']
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
