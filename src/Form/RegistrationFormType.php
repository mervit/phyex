<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('birthYear', NumberType::class, [
                'html5' => true,
                'attr' => [
                    'min' => 1850,
                    'max' => date('Y')
                ]
            ])
            ->add('yearsOfExperience', NumberType::class, [
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 50
                ]
            ])
            ->add('student', CheckboxType::class, [
                'required' => false
            ])
            ->add('currentEducationLevel', ChoiceType::class, [
                'choices' => [
                    'Bc' => 'bc',
                    'Mgr' => 'mgr',
                    'None' => ''
                ]
            ])
            ->add('academicYear', NumberType::class, [
                'required' => false,
                'html5' => true,
                'attr' => [
                    'min' => 1,
                    'max' => 8
                ]
            ])
            ->add('universityName', TextType::class)
            ->add('facultyName', TextType::class)
            ->add('studyProgram', TextType::class, [
                'required' => false,
            ])
            ->add('fieldOfExperience', TextType::class)
            ->add('country', ChoiceType::class, [
                'choices' => [
                    "Czechia" => 'czech',
                    'Slovakia' => 'slovak',
                    'Germany' => 'germany',
                    'Austria' => 'austria',
                    "Other" => 'other'
                ]
            ])
            ->add('courseList', TextareaType::class, [
                'required' => false,
            ])
            ->add('favoriteMethod', TextType::class, [
                'required' => false,
            ])
            ->add('stayInTouch', CheckboxType::class, [
                'required' => false
            ])
            ->add('currentJobTitle', TextType::class, [
                'required' => false
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
