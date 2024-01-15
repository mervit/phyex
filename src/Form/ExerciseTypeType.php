<?php
namespace App\Form;

use App\Entity\ExerciseType;
use App\Entity\ExerciseTypeCategory;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ExerciseTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', TextType::class)
            ->add('code', TextType::class)
            ->add('description', TextareaType::class)
            ->add('enabled', ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No' => false
                ]
            ])
            ->add('exerciseTypeParams', CollectionType::class, [
                'entry_type' => ExerciseTypeParamType::class,
                'label' => false,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true
            ])
            ->add('categories', EntityType::class, [
                'class' => ExerciseTypeCategory::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('etc')
                        ->andWhere('etc.deleted = 0');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ])
            ->add('instructionVideo', FileType::class, [
                'required' => $options['require_instruction_video'],
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '51200k',
                        'mimeTypes' => [
                            'video/mp4'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid MP4 video file',
                    ])
                ],
            ])
            ->add('defaultVideoView', ChoiceType::class, [
                'choices' => [
                    'Middle left' => 'midLeft',
                    'Middle right' => 'midRight',
                    'Front' => 'front',
                    'Side' => 'side'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExerciseType::class,
            'require_instruction_video' => false
        ]);

        $resolver->setAllowedTypes('require_instruction_video', 'bool');
    }
}