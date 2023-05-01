<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Entity\ExerciseType;
use App\Entity\Figurant;
use App\Entity\User;
use App\Repository\ExerciseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\ExercisePasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[IsGranted('ROLE_ADMIN_EXERCISES')]
class ExerciseController extends AbstractController
{
    #[Route('/exercises/list', name: 'app_exercises')]
    public function list(
        ExerciseRepository $exerciseRepository,
        Request $request
    ): Response
    {
        $enabled_order_fields = ['e.id', 'e.datetime'];
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
        $filter_form_builder->add('datetime_from', DateTimeType::class, [
            'required' => false
        ]);
        $filter_form_builder->add('datetime_to', DateTimeType::class, [
            'required' => false
        ]);
        $filter_form_builder->add('figurant', EntityType::class, [
            'required' => false,
            'class' => Figurant::class,
            'choice_label' => 'id'
        ]);
        $filter_form_builder->add('exerciseType', EntityType::class, [
            'required' => false,
            'class' => ExerciseType::class,
            'choice_label' => 'name'
        ]);
        $filter_form_builder->add('submit', SubmitType::class, [
            'label' => 'Search',
            'attr' => [
                'class' => 'btn btn-primary mt-2'
            ]
        ]);

        $criteria = new \Doctrine\Common\Collections\Criteria();

        $filter_form = $filter_form_builder->getForm();
        $filter_form->handleRequest($request);
        if($filter_form->isSubmitted() && $filter_form->isValid()){

            if($filter_form->get('datetime_from')->getData()){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->gt('datetime', $filter_form->get('datetime_from')->getData()));
            }
            if($filter_form->get('datetime_to')->getData()){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->lt('datetime', $filter_form->get('datetime_to')->getData()));
            }
            if($filter_form->get('figurant')->getData()){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->eq('figurant', $filter_form->get('figurant')->getData()));
            }

            if($filter_form->get('exerciseType')->getData()){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->eq('exerciseType', $filter_form->get('exerciseType')->getData()));
            }

        }

        $max_exercises = $exerciseRepository->countByCriteria($criteria);

        $exercises = $exerciseRepository->findByCriteria($criteria, $order, $rows_on_page, ($page - 1) * $rows_on_page);

        return $this->render('exercise/list.html.twig', [
            'filter_form' => $filter_form->createView(),
            'exercises' => $exercises,
            'page' => $page,
            'max_page' => $max_exercises == 0 ? 1 : ceil($max_exercises / $rows_on_page)
        ]);
    }

}
