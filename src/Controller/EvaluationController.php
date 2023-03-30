<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\EvaluationParam;
use App\Repository\EvaluationRepository;
use App\Repository\ExerciseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
                    'label' => $param->getName(),
                    'data' => '0',
                    'constraints' => [
                        new EqualTo([1, 2, 3])
                    ]
                ]);
            }

            $previousForm->add('submit', SubmitType::class, [
                'label' => 'Send evaluation'
            ]);

            $previousForm->add('exercise', HiddenType::class, [
                'data' => $e->getId()
            ]);

            $form = $previousForm->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                $evaluation = new Evaluation();
                $evaluation->setExercise($e);
                $evaluation->setUser($this->getUser());

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
        $exercise = $exerciseRepository->getRandom();

        $formBuilder = $this->createFormBuilder();

        foreach($exercise->getExerciseType()->getExerciseTypeParams() as $param){
            $formBuilder->add('param' . $param->getId(), NumberType::class, [
                'label' => $param->getName(),
                'data' => '0',
                'constraints' => [
                    new EqualTo([1,2,3])
                ]
            ]);
        }

        $formBuilder->add('submit', SubmitType::class, [
            'label' => 'Send evaluation'
        ]);

        $formBuilder->add('exercise', HiddenType::class, [
            'data' => $exercise->getId()
        ]);


        return $this->render('evaluation/index.html.twig', [
            'exercise' => $exercise,
            'evaluationForm' => $formBuilder->getForm()
        ]);
    }
}
