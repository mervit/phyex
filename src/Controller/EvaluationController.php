<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\EvaluationParam;
use App\Entity\ExerciseType;
use App\Entity\User;
use App\Repository\EvaluationRepository;
use App\Repository\ExerciseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\EqualTo;

class EvaluationController extends AbstractController
{
    #[Route('/evaluation', name: 'app_evaluation')]
    public function index(
        Request $request,
        ExerciseRepository $exerciseRepository,
        EvaluationRepository $evaluationRepository
    ): Response
    {

        if( $request->get('form') ) {

            $e = $exerciseRepository->find($request->get('form')['exercise']);

            $previousForm = $this->createFormBuilder();

            foreach ($e->getExerciseType()->getExerciseTypeParams() as $param) {
                $previousForm->add('param' . $param->getId(), NumberType::class, [
                    'data' => '0',
                    'constraints' => [
                        new EqualTo(range(1, $this->getParameter('number_of_stars')))
                    ]
                ]);
            }

            $previousForm->add('comment', TextareaType::class);

            $previousForm->add('exercise', HiddenType::class);

            $previousForm->add('submit', SubmitType::class);

            $form = $previousForm->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted()) {

                $data = $form->getData();
                $evaluation = new Evaluation();
                $evaluation->setExercise($e);
                $evaluation->setUser($this->getUser());
                $evaluation->setComment($data['comment']);

                foreach ($e->getExerciseType()->getExerciseTypeParams() as $param) {
                    $evaluationParam = new EvaluationParam();
                    $evaluationParam->setExerciseTypeParam($param);
                    $evaluationParam->setValue($data['param' . $param->getId()]);
                    $evaluation->addEvaluationParam($evaluationParam);
                }

                $evaluationRepository->save($evaluation, true);

                $this->addFlash('success', 'Evaluation saved.');

            }

        }
        $exercise = $exerciseRepository->getRandom($this->getUser());

        $formBuilder = $this->createFormBuilder();

        if($exercise) {

            foreach ($exercise->getExerciseType()->getExerciseTypeParams() as $param) {
                $formBuilder->add('param' . $param->getId(), NumberType::class, [
                    'label' => $param->getName(),
                    'data' => '0',
                    'constraints' => [
                        new EqualTo(range(1, $this->getParameter('number_of_stars')))
                    ]
                ]);
            }

            $formBuilder->add('exercise', HiddenType::class, [
                'data' => $exercise->getId()
            ]);

            $formBuilder->add('comment', TextareaType::class, [
                'required' => false
            ]);

            $formBuilder->add('submit', SubmitType::class, [
                'label' => 'Send evaluation'
            ]);

        }

        return $this->render('evaluation/index.html.twig', [
            'exercise' => $exercise,
            'evaluationForm' => $formBuilder->getForm(),
            'number_of_stars' => $this->getParameter('number_of_stars')
        ]);
    }

    #[IsGranted('ROLE_ADMIN_EVALUATIONS')]
    #[Route('/evaluations/list', name: 'app_evaluations')]
    public function list(
        EvaluationRepository $evaluationRepository,
        Request $request
    ): Response
    {
        $enabled_order_fields = ['e.id', 'e.created'];
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
        $filter_form_builder->add('created_from', DateTimeType::class, [
            'required' => false
        ]);
        $filter_form_builder->add('created_to', DateTimeType::class, [
            'required' => false
        ]);
        $filter_form_builder->add('user', EntityType::class, [
            'required' => false,
            'class' => User::class,
            'choice_label' => 'email'
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
            if($filter_form->get('created_from')->getData()){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->gt('created', $filter_form->get('created_from')->getData()));
            }
            if($filter_form->get('created_to')->getData()){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->lt('created', $filter_form->get('created_to')->getData()));
            }
            if($filter_form->get('user')->getData()){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->eq('user', $filter_form->get('user')->getData()));
            }

            if($filter_form->get('exerciseType')->getData()){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->eq('exercise.exerciseType', $filter_form->get('exerciseType')->getData()));
            }

        }

        $max_evaluations = $evaluationRepository->countByCriteria($criteria);

        $evaluations = $evaluationRepository->findByCriteria($criteria, $order, $rows_on_page, ($page - 1) * $rows_on_page);

        return $this->render('evaluation/list.html.twig', [
            'filter_form' => $filter_form->createView(),
            'evaluations' => $evaluations,
            'page' => $page,
            'max_page' => $max_evaluations == 0 ? 1 : ceil($max_evaluations / $rows_on_page)
        ]);
    }

}
