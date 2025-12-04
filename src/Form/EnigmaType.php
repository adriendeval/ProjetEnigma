<?php

namespace App\Form;

use App\Entity\Enigma;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnigmaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'label',
                'label' => 'Type d\'énigme'
            ])
            ->add('order', IntegerType::class, [
                'label' => 'Ordre d\'affichage'
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('instruction', TextareaType::class, [
                'label' => 'Instructions',
                'attr' => ['rows' => 4]
            ])
            ->add('secretCode', TextType::class, [
                'label' => 'Code secret'
            ])
            ->add('data', TextareaType::class, [
                'label' => 'Données JSON',
                'attr' => ['rows' => 10],
                'help' => 'Format JSON pour les données spécifiques de l\'énigme'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enigma::class,
        ]);
    }
}
