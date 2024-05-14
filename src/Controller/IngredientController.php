<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class IngredientController extends AbstractController
{
    /**
     * This function displays all the ingredients in the database.
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }
    /**
     * This controller creates a new ingredient.
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', name: 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {   
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form -> getData();

            $manager -> persist($ingredient);
            $manager -> flush();

            $this->addFlash('success', 'Ingrédient ajouté avec succès');

            return $this->redirectToRoute('ingredient');
        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/ingredient/edition/{id}', name:'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $manager) : Response
    {
        
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form -> getData();

            $manager -> persist($ingredient);
            $manager -> flush();

            $this->addFlash('success', 'Ingrédient modifié avec succès');

            return $this->redirectToRoute('ingredient');
        }
        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/suppression/{id}', name: 'ingredient.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient) : Response
    {
        if(!$ingredient){
            $this->addFlash('danger', 'Ingrédient introuvable');
            return $this->redirectToRoute('ingredient');
        }
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash('success', 'Ingrédient supprimé avec succès');

        return $this->redirectToRoute('ingredient');
    }
}
