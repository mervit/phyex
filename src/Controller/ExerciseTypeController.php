<?php

namespace App\Controller;

use App\Entity\ExerciseType;
use App\Entity\ExerciseTypeCategory;
use App\Entity\ExerciseTypeParam;
use App\Form\ExerciseTypeParamType;
use App\Form\ExerciseTypeType;
use App\Repository\ExerciseRepository;
use App\Repository\ExerciseTypeRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\ExerciseTypePasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[IsGranted('ROLE_ADMIN_EXERCISE_TYPES')]
class ExerciseTypeController extends AbstractController
{
    #[Route('/exercise-types/list', name: 'app_exercise_types')]
    public function list(
        ExerciseTypeRepository $exerciseTypeRepository,
        Request $request
    ): Response
    {
        $enabled_order_fields = ['et.id'];
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
        $filter_form_builder->add('name', TextType::class, [
            'required' => false
        ]);
        $filter_form_builder->add('code', TextType::class, [
            'required' => false
        ]);
        $filter_form_builder->add('enabled', ChoiceType::class, [
            'required' => false,
            'choices' => [
                'Only enabled' => true,
                'Only disabled' => false
            ]
        ]);
        $filter_form_builder->add('category', EntityType::class, [
            'class' => ExerciseTypeCategory::class,
            'query_builder' => function (EntityRepository $er): QueryBuilder {
                return $er->createQueryBuilder('etc')
                    ->andWhere('etc.deleted = 0');
            },
            'choice_label' => 'name',
            'required' => false
        ]);
        $filter_form_builder->add('submit', SubmitType::class, [
            'label' => 'Search',
            'attr' => [
                'class' => 'btn btn-primary mt-2'
            ]
        ]);

        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria->where(\Doctrine\Common\Collections\Criteria::expr()->eq('et.deleted', false));

        $filter_form = $filter_form_builder->getForm();
        $filter_form->handleRequest($request);
        $category = null;
        if($filter_form->isSubmitted() && $filter_form->isValid()){

            if($filter_form->get('name')->getData()){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->contains('name', $filter_form->get('name')->getData()));
            }

            if($filter_form->get('enabled')->getData() !== null){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->eq('enabled', $filter_form->get('enabled')->getData()));
            }

            if($filter_form->get('code')->getData()){
                $criteria->andWhere(\Doctrine\Common\Collections\Criteria::expr()->contains('code', $filter_form->get('code')->getData()));
            }

            if($filter_form->get('category')->getData()){
                $category = $filter_form->get('category')->getData();
            }

        }

        $max_exerciseTypes = $exerciseTypeRepository->countByCriteria($criteria, $category);

        $exerciseTypes = $exerciseTypeRepository->findByCriteria($criteria, $category, $order, $rows_on_page, ($page - 1) * $rows_on_page);

        return $this->render('exercise_type/list.html.twig', [
            'filter_form' => $filter_form->createView(),
            'exerciseTypes' => $exerciseTypes,
            'page' => $page,
            'max_page' => $max_exerciseTypes == 0 ? 1 : ceil($max_exerciseTypes / $rows_on_page)
        ]);
    }

    #[Route('/exercise_type/edit/{id}', name: 'app_exercise_type_edit')]
    public function edit(
        int $id,
        ExerciseTypeRepository $exerciseTypeRepository,
        ExerciseRepository $exerciseRepository,
        SluggerInterface $slugger,
        Request $request
    ){

        $exerciseType = $exerciseTypeRepository->find($id);

        if(!$exerciseType){

            $this->addFlash('danger', 'ExerciseType does not exists');

            return $this->redirectToRoute('app_exercise_types');

        }

        $count = $exerciseRepository->countByExerciseType($exerciseType);
        if($count > 0){

            $this->addFlash('danger', 'ExerciseType is now unable to edit');

            return $this->redirectToRoute('app_exercise_types');

        }

        $form = $this->createForm(ExerciseTypeType::class, $exerciseType);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            /** @var ExerciseType $exerciseType */
            $exerciseType = $form->getData();

            /** @var UploadedFile $instructionVideoFile */
            $instructionVideoFile = $form->get('instructionVideo')->getData();

            if ($instructionVideoFile) {

                $originalFilename = pathinfo($instructionVideoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$instructionVideoFile->guessExtension();

                // Move the file to the directory where instructionVideos are stored
                try {
                    $instructionVideoFile->move(
                        $this->getParameter('relative_uploads_directory') . '/instruction_videos/',
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'File cannot be uploaded');
                }

                $exerciseType->setInstructionVideo($newFilename);
            }

            $exerciseTypeRepository->save($exerciseType, true);
            $this->addFlash('success', 'ExerciseType created');

            return $this->redirectToRoute('app_exercise_types');

        }

        return $this->render('exercise_type/item.html.twig', [
            'form' => $form->createView()
        ]);

    }

    #[Route('/exercise_type/add', name: 'app_exercise_type_add')]
    public function add(
        ExerciseTypeRepository $exerciseTypeRepository,
        SluggerInterface $slugger,
        Request $request
    ){

        $exerciseType = new ExerciseType();

        $form = $this->createForm(ExerciseTypeType::class, $exerciseType, ['require_instruction_video' => true]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            /** @var ExerciseType $exerciseType */
            $exerciseType = $form->getData();

            /** @var UploadedFile $instructionVideoFile */
            $instructionVideoFile = $form->get('instructionVideo')->getData();

            $originalFilename = pathinfo($instructionVideoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$instructionVideoFile->guessExtension();

            // Move the file to the directory where instructionVideos are stored
            try {
                $instructionVideoFile->move(
                    $this->getParameter('relative_uploads_directory') . '/instruction_videos/',
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('danger', 'File cannot be uploaded');
            }

            $exerciseType->setInstructionVideo($newFilename);

            $exerciseTypeRepository->save($exerciseType, true);
            $this->addFlash('success', 'ExerciseType created');

            return $this->redirectToRoute('app_exercise_types');

        }

        return $this->render('exercise_type/item.html.twig', [
            'form' => $form->createView()
        ]);

    }


    #[Route('/exercise_type/delete/{id}', name: 'app_exercise_type_delete')]
    public function delete(
        int $id,
        ExerciseTypeRepository $exerciseTypeRepository
    ){

        $exerciseType = $exerciseTypeRepository->find($id);

        if(!$exerciseType){

            $this->addFlash('danger', 'ExerciseType does not exists');

            return $this->redirectToRoute('app_exercise_types');

        }

        if($exerciseType->isDeleted()){

            $this->addFlash('danger', 'ExerciseType is already deleted');

            return $this->redirectToRoute('app_exercise_types');

        }

        $exerciseType->setDeleted(true);
        $exerciseTypeRepository->save($exerciseType, true);

        $this->addFlash('success', 'ExerciseType was removed');

        return $this->redirectToRoute('app_exercise_types');

    }

    #[Route('/exercise_type/enable/{id}', name: 'app_exercise_type_enable')]
    public function enable(
        int $id,
        ExerciseTypeRepository $exerciseTypeRepository
    ){

        $exerciseType = $exerciseTypeRepository->find($id);

        if(!$exerciseType){

            $this->addFlash('danger', 'ExerciseType does not exists');

            return $this->redirectToRoute('app_exercise_types');

        }

        if($exerciseType->isDeleted()){

            $this->addFlash('danger', 'ExerciseType is deleted');

            return $this->redirectToRoute('app_exercise_types');

        }

        if($exerciseType->isEnabled()){

            $this->addFlash('danger', 'ExerciseType is already enabled');

            return $this->redirectToRoute('app_exercise_types');

        }

        $exerciseType->setEnabled(true);
        $exerciseTypeRepository->save($exerciseType, true);

        $this->addFlash('success', 'ExerciseType was enabled');

        return $this->redirectToRoute('app_exercise_types');

    }

    #[Route('/exercise_type/disable/{id}', name: 'app_exercise_type_disable')]
    public function disable(
        int $id,
        ExerciseTypeRepository $exerciseTypeRepository
    ){

        $exerciseType = $exerciseTypeRepository->find($id);

        if(!$exerciseType){

            $this->addFlash('danger', 'ExerciseType does not exists');

            return $this->redirectToRoute('app_exercise_types');

        }

        if($exerciseType->isDeleted()){

            $this->addFlash('danger', 'ExerciseType is deleted');

            return $this->redirectToRoute('app_exercise_types');

        }

        if(!$exerciseType->isEnabled()){

            $this->addFlash('danger', 'ExerciseType is already disabled');

            return $this->redirectToRoute('app_exercise_types');

        }

        $exerciseType->setEnabled(false);
        $exerciseTypeRepository->save($exerciseType, true);

        $this->addFlash('success', 'ExerciseType was disabled');

        return $this->redirectToRoute('app_exercise_types');

    }

}
