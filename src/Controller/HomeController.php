<?php

namespace App\Controller;

use App\Repository\EvaluationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]

    public function index(
        EvaluationRepository $evaluationRepository
    ): Response
    {

        $numberOfEvaluations = $evaluationRepository->getNumberOfUsersEvaluations($this->getUser());
        $lastEvaluation = $evaluationRepository->getLastEvaluation($this->getUser());
        $numberOfLastEvaluations = $evaluationRepository->getNumberOfUsersEvaluationsForLastDays($this->getUser(), 7);

        $chartData = [];
        for($i = 30; $i >= 0; --$i){

            $day = new \DateTime('-' . $i . ' days');

            $chartData[$day->format('d.m.Y')] = $evaluationRepository->getNumberOfEvaluationsInDay($this->getUser(), $day);

        }

        return $this->render('home/index.html.twig', [
            'numberOfEvaluations' => $numberOfEvaluations,
            'lastEvaluation' => $lastEvaluation,
            'numberOfLastEvaluations' => $numberOfLastEvaluations,
            'chartData' => $chartData

        ]);
    }
}
