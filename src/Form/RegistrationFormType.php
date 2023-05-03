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
            ->add('email', EmailType::class, [
                'help' => 'Fill your email address. That be used for login. We will not send you any unsolicited messages.'
            ])
            ->add('plainPassword', PasswordType::class, [
                'help' => 'Fill your password used for login. Password need to be at least 6 characters long.',
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
            ->add('birthYear', NumberType::class, [
                'help' => 'Set you birth year. Possible values are from 1980 to 2023.',
                'html5' => true,
                'attr' => [
                    'min' => 1850,
                    'max' => date('Y')
                ]
            ])
            ->add('country', ChoiceType::class, [
                'help' => 'Select one of the countries.',
                'choices' => [
                    "Czechia" => 'czech',
                    'Slovakia' => 'slovak',
                    'Germany' => 'germany',
                    'Austria' => 'austria',
                    "Other" => 'other'
                ]
            ])

            ->add('completedEducationLevel', ChoiceType::class, [
                'help' => 'Select last completed education level.',
                'choices' => [
                    'Bc.' => 'bc',
                    'Mgr.' => 'mgr',
                    'Dr.' => 'dr',
                    'Phd.' => 'phd',
                    'None' => ''
                ]
            ])
            ->add('completedEducationUniversityName', TextType::class, [
                'help' => 'Fill name of university where you completed your last education.',
                'required' => false
            ])
            ->add('completedEducationFacultyName', TextType::class, [
                'help' => 'Fill name of faculty where you completed your last education.',
                'required' => false
            ])

            ->add('currentEducationLevel', ChoiceType::class, [
                'help' => 'If you are currently student please fill education level you currently studying.',
                'choices' => [
                    'Bc.' => 'bc',
                    'Mgr.' => 'mgr',
                    'Dr.' => 'dr',
                    'Phd.' => 'phd',
                    'None' => ''
                ]
            ])
            ->add('studyProgram', TextType::class, [
                'help' => 'Name of your current study program.',
                'required' => false,
            ])
            ->add('academicYear', NumberType::class, [
                'help' => 'Set in which year you currently are.',
                'required' => false,
                'html5' => true,
                'attr' => [
                    'min' => 1,
                    'max' => 8
                ]
            ])
            ->add('universityName', TextType::class, [
                'help' => 'Fill name of university you are currently studying.',
                'required' => false
            ])
            ->add('facultyName', TextType::class, [
                'help' => 'Fill name of faculty you are currently studying.',
                'required' => false
            ])

            ->add('fieldOfExperience', TextType::class, [
                'help' => 'Fill in which field you have some experiences. For example: teaching, sport physiotherapy, post-trauma physiotherapy atc.. '
            ])
            ->add('yearsOfExperience', NumberType::class, [
                'help' => 'Fill number of years of experience you have in that field.',
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 50
                ]
            ])
            ->add('courseList', TextareaType::class, [
                'help' => 'Fill all your completed special courses if you have some.',
                'required' => false,
            ])
            ->add('favoriteMethod', TextType::class, [
                'help' => 'If you have some favorite method of physiotherapy please fill the name of it here.',
                'required' => false,
            ])
            ->add('currentJobTitle', TextType::class, [
                'help' => 'If you are currently working in physiotherapy field, please fill the name of you work position.',
                'required' => false
            ])

            ->add('stayInTouch', CheckboxType::class, [
                'help' => 'Tell us if you want to be inform with physiotherapy video evaluation results.',
                'required' => false
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'help_html' => true,
                'help' => 'You need to agree terms and conditions. You can found them <a href="/terms-and-conditions.pdf" target="_blank">here</a>.',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
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
