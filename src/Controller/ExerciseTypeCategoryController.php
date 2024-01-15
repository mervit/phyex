<?php

namespace App\Controller;

use App\Entity\ExerciseType;
use App\Entity\ExerciseTypeCategory;
use App\Entity\ExerciseTypeParam;
use App\Form\ExerciseTypeCategoryType;
use App\Form\ExerciseTypeParamType;
use App\Form\ExerciseTypeType;
use App\Repository\ExerciseRepository;
use App\Repository\ExerciseTypeCategoryRepository;
use App\Repository\ExerciseTypeRepository;
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
class ExerciseTypeCategoryController extends AbstractController
{
    #[Route('/exercise-type-categories/list', name: 'app_exercise_type_categories')]
    public function list(
        ExerciseTypeCategoryRepository $categoryRepository,
        Request $request
    ): Response
    {
        $rows_on_page = 20;
        $page = 1;
        $order = null;
        if($request->get('page')){
            $page = $request->get('page');
        }

        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria->where(\Doctrine\Common\Collections\Criteria::expr()->eq('c.deleted', false));

        $max_categories = $categoryRepository->countByCriteria($criteria);

        $categories = $categoryRepository->findByCriteria($criteria, $order, $rows_on_page, ($page - 1) * $rows_on_page);

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
            'page' => $page,
            'max_page' => $max_categories == 0 ? 1 : ceil($max_categories / $rows_on_page)
        ]);
    }

    #[Route('/exercise_type_category/edit/{id}', name: 'app_exercise_type_category_edit')]
    public function edit(
        int $id,
        ExerciseTypeCategoryRepository $categoryRepository,
        Request $request
    ){

        $category = $categoryRepository->find($id);

        if(!$category){

            $this->addFlash('danger', 'Exercise Type Category does not exists');

            return $this->redirectToRoute('app_exercise_type_categories');

        }

        $form = $this->createForm(ExerciseTypeCategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            /** @var ExerciseTypeCategory $category */
            $category = $form->getData();

            $categoryRepository->save($category, true);
            $this->addFlash('success', 'Exercise Type Category created');

            return $this->redirectToRoute('app_exercise_type_categories');

        }

        return $this->render('category/item.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);

    }

    #[Route('/exercise_type_category/add', name: 'app_exercise_type_category_add')]
    public function add(
        ExerciseTypeCategoryRepository $categoryRepository,
        Request $request
    ){

        $category = new ExerciseTypeCategory();

        $form = $this->createForm(ExerciseTypeCategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            /** @var ExerciseTypeCategory $category */
            $category = $form->getData();

            $categoryRepository->save($category, true);
            $this->addFlash('success', 'Exercise Type Category created');

            return $this->redirectToRoute('app_exercise_type_categories');

        }

        return $this->render('category/item.html.twig', [
            'form' => $form->createView()
        ]);

    }


    #[Route('/exercise_type_category/delete/{id}', name: 'app_exercise_type_category_delete')]
    public function delete(
        int $id,
        ExerciseTypeCategoryRepository $categoryRepository
    ){

        $category = $categoryRepository->find($id);

        if(!$category){

            $this->addFlash('danger', 'Exercise Type Category does not exists');

            return $this->redirectToRoute('app_exercise_type_categories');

        }

        if($category->isDeleted()){

            $this->addFlash('danger', 'Exercise Type Category is deleted');

            return $this->redirectToRoute('app_exercise_type_categories');

        }

        $category->setDeleted(true);
        $categoryRepository->save($category, true);

        $this->addFlash('success', 'Exercise Type Category was removed');

        return $this->redirectToRoute('app_exercise_type_categories');

    }

    #[Route('/exercise_type_category/enable/{id}', name: 'app_exercise_type_category_enable')]
    public function enable(
        int $id,
        ExerciseTypeCategoryRepository $categoryRepository,
        ExerciseTypeRepository $exerciseTypeRepository
    ){

        $category = $categoryRepository->find($id);

        if(!$category){

            $this->addFlash('danger', 'Exercise Type Category does not exists');

            return $this->redirectToRoute('app_exercise_type_categories');

        }

        if($category->isDeleted()){

            $this->addFlash('danger', 'Exercise Type Category is deleted');

            return $this->redirectToRoute('app_exercise_type_categories');

        }

        $i = 0;
        $exerciseTypes = $exerciseTypeRepository->findByCategory($category);
        foreach ($exerciseTypes as $exerciseType) {
            $exerciseType->setEnabled(true);
            $exerciseTypeRepository->save($exerciseType, true);
            $i++;
        }


        $this->addFlash('success', $i . ' exercise types was enabled');

        return $this->redirectToRoute('app_exercise_type_categories');

    }

    #[Route('/exercise_type_category/disable/{id}', name: 'app_exercise_type_category_disable')]
    public function disable(
        int $id,
        ExerciseTypeCategoryRepository $categoryRepository,
        ExerciseTypeRepository $exerciseTypeRepository
    ){

        $category = $categoryRepository->find($id);

        if(!$category){

            $this->addFlash('danger', 'Exercise Type Category does not exists');

            return $this->redirectToRoute('app_exercise_type_categories');

        }

        if($category->isDeleted()){

            $this->addFlash('danger', 'Exercise Type Category is deleted');

            return $this->redirectToRoute('app_exercise_type_categories');

        }

        $i = 0;
        $exerciseTypes = $exerciseTypeRepository->findByCategory($category);
        foreach ($exerciseTypes as $exerciseType) {
            $exerciseType->setEnabled(false);
            $exerciseTypeRepository->save($exerciseType, true);
            $i++;
        }


        $this->addFlash('success', $i . ' exercise types was disabled');

        return $this->redirectToRoute('app_exercise_type_categories');

    }

}
