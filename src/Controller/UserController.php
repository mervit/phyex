<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[IsGranted('ROLE_ADMIN_USERS')]
class UserController extends AbstractController
{
    #[Route('/users/list', name: 'app_users')]
    public function list(
        UserRepository $userRepository,
        Request $request
    ): Response
    {
        $enabled_order_fields = ['id', 'email'];
        $rows_on_page = 20;
        $page = 1;
        $order = null;
        if($request->get('order_by')){
            if(in_array($request->get('order_by'), $enabled_order_fields)) {
                if($request->get('order_direction') == 'asc'){
                    $order[$request->get('order_by')] = 'ASC';
                } else {
                    $order[$request->get('order_by')] = 'DESC';
                }
            }
        }
        if($request->get('page')){
            $page = $request->get('page');
        }
        $filter_form_builder = $this->createFormBuilder();
        $filter_form_builder->add('verified', ChoiceType::class, [
            'choices' => [
                'All' => 'none',
                'Verified only' => 'verified',
                'Non verified only' => 'nonverified'
            ]
        ]);
        $filter_form_builder->add('email', TextType::class, [
            'required' => false
        ]);
        $filter_form_builder->add('submit', SubmitType::class, [
            'label' => 'Search',
            'attr' => [
                'class' => 'btn btn-primary mt-2'
            ]
        ]);

        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria->where(\Doctrine\Common\Collections\Criteria::expr()->eq('deleted', false));

        $filter_form = $filter_form_builder->getForm();
        $filter_form->handleRequest($request);
        if($filter_form->isSubmitted() && $filter_form->isValid()){

            if($filter_form->get('email')){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->contains('email', $filter_form->get('email')->getData()));
            }

            if($filter_form->get('verified')){
                if($filter_form->get('verified')->getData() == 'verified') {
                    $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->eq('isVerified', true));
                } elseif($filter_form->get('verified')->getData() == 'nonverified') {
                    $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->eq('isVerified', false));
                }
            }

        }

        $max_users = $userRepository->countByCriteria($criteria);

        $users = $userRepository->findByCriteria($criteria, $order, $rows_on_page, ($page - 1) * $rows_on_page);

        return $this->render('user/list.html.twig', [
            'filter_form' => $filter_form->createView(),
            'users' => $users,
            'page' => $page,
            'max_page' => $max_users == 0 ? 1 : ceil($max_users / $rows_on_page)
        ]);
    }

    #[Route('/user/verify/{id}', name: 'app_users_verify')]
    public function verify(
        int $id,
        UserRepository $userRepository
    ){

        $user = $userRepository->find($id);

        if(!$user){

            $this->addFlash('danger', 'User does not exists');

            return $this->redirectToRoute('app_users');

        }

        if($user->isVerified()){

            $this->addFlash('danger', 'User is already verified');

            return $this->redirectToRoute('app_users');

        }

        $user->setIsVerified(true);
        $userRepository->save($user, true);

        $this->addFlash('success', 'User was verified');

        return $this->redirectToRoute('app_users');

    }

    #[Route('/user/edit/{id}', name: 'app_users_edit')]
    public function edit(
        int $id,
        UserRepository $userRepository,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher
    ){

        $user = $userRepository->find($id);

        if(!$user){

            $this->addFlash('danger', 'User does not exists');

            return $this->redirectToRoute('app_users');

        }

        $formBuilder = $this->createFormBuilder($user);

        $formBuilder
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'Users' => 'ROLE_ADMIN_USERS',
                    'Evaluations' => 'ROLE_ADMIN_EVALUATIONS',
                    'Exercises' => 'ROLE_ADMIN_EXERCISES',
                    'Exercise types' => 'ROLE_ADMIN_EXERCISE_TYPES'
                ]
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
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user = $form->getData();

            if($form->get('plainPassword')->getData()) {

                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            $userRepository->save($user, true);
            $this->addFlash('success', 'User updated');

        }

        return $this->render('user/item.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);

    }


    #[Route('/user/delete/{id}', name: 'app_users_delete')]
    public function delete(
        int $id,
        UserRepository $userRepository
    ){

        $user = $userRepository->find($id);

        if(!$user){

            $this->addFlash('danger', 'User does not exists');

            return $this->redirectToRoute('app_users');

        }

        if($user->isDeleted()){

            $this->addFlash('danger', 'User is already deleted');

            return $this->redirectToRoute('app_users');

        }

        $user->setDeleted(true);
        $userRepository->save($user, true);

        $this->addFlash('success', 'User was removed');

        return $this->redirectToRoute('app_users');

    }

}
