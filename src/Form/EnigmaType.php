<?php

namespace App\Form;

use App\Entity\Type;
use App\Entity\Enigma;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EnigmaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérer les données initiales de l'énigme
        $enigma = $options['data'] ?? null;
        $existingData = $enigma ? $enigma->getData() : [];
        
        // Préparer les données pour chaque collection
        $mcqData = [];
        if (isset($existingData['questions'])) {
            foreach ($existingData['questions'] as $q) {
                $mcqData[] = [
                    'question' => $q['question'] ?? '',
                    'answer0' => $q['answers'][0] ?? '',
                    'answer1' => $q['answers'][1] ?? '',
                    'answer2' => $q['answers'][2] ?? '',
                    'answer3' => $q['answers'][3] ?? '',
                    'correct' => $q['correct'] ?? 0,
                ];
            }
        }
        
        $timelineData = $existingData['items'] ?? [];
        $photoData = $existingData['pairs'] ?? [];

        $builder
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'label',
                'label' => 'Type d\'énigme',
                'attr' => ['data-action' => 'change->enigma-form#typeChanged']
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
            // Hidden field to store the final JSON
            ->add('data', TextareaType::class, [
                'label' => 'Données JSON (Debug)',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'hidden'],
                'label_attr' => ['class' => 'hidden']
            ])
            // Dynamic Collections avec données initiales
            ->add('mcqQuestions', CollectionType::class, [
                'entry_type' => McqQuestionType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'mapped' => false,
                'label' => false,
                'data' => $mcqData,
            ])
            ->add('timelineItems', CollectionType::class, [
                'entry_type' => TimelineItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'mapped' => false,
                'label' => false,
                'data' => $timelineData,
            ])
            ->add('photoPairs', CollectionType::class, [
                'entry_type' => PhotoPairType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'mapped' => false,
                'label' => false,
                'data' => $photoData,
            ])
        ;

        // Event Listener to save the unmapped fields back to the JSON data
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $enigma = $event->getData();
            $form = $event->getForm();

            $type = $enigma->getType() ? $enigma->getType()->getLabel() : null;
            $newData = [];

            if ($type === 'mcq') {
                $questions = $form->get('mcqQuestions')->getData();
                $newData['questions'] = [];
                foreach ($questions as $q) {
                    $answers = [
                        $q['answer0'] ?? '',
                        $q['answer1'] ?? '',
                        $q['answer2'] ?? '',
                        $q['answer3'] ?? ''
                    ];
                    $newData['questions'][] = [
                        'question' => $q['question'] ?? '',
                        'answers' => $answers,
                        'correct' => isset($q['correct']) ? (int)$q['correct'] : 0
                    ];
                }
                $enigma->setData($newData);
            } elseif ($type === 'timeline') {
                $items = $form->get('timelineItems')->getData();
                $newData['items'] = [];
                foreach ($items as $index => $item) {
                    $item['id'] = $index + 1;
                    $newData['items'][] = $item;
                }
                $enigma->setData($newData);
            } elseif ($type === 'photo') {
                $pairs = $form->get('photoPairs')->getData();
                $newData['pairs'] = [];
                foreach ($pairs as $index => $pair) {
                    $pair['id'] = $index;
                    $newData['pairs'][] = $pair;
                }
                $enigma->setData($newData);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enigma::class,
        ]);
    }
}
