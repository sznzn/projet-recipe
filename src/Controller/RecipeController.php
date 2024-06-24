<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index', methods:['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(['user'=> $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/recette/publique', 'recipe.public', methods:['GET'])]
    public function indexPublic(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ) : Response
    {
        $recipes = $paginator->paginate(
            $repository->findPublicRecipe(null),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index_public.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/recette/creation', 'recipe.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manage ) : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe, ['submit_label' => 'Créer ma recette OK']);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());

            $manage->persist($recipe);
            $manage->flush();

            $this->addFlash('success', 'Recette ajouté avec succès');

            return $this->redirectToRoute('recipe.index');
        };


        //errors
        if($form->isSubmitted() && !$form->isValid()){
            $errors = [];
            foreach($form->getErrors(true) as $error){
                $errors[] = $error->getMessage();
            }
            dd($errors);
        };
        


        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[IsGranted(attribute:new Expression('is_granted("ROLE_USER") and subject.isIsPublic() === true || user === subject.getUser()'), subject:'recipe')]
    #[Route('/recette/{id}', 'recipe.show', methods:['GET', 'POST'])]
    public function show(Recipe $recipe, Request $request, MarkRepository $markRepository, EntityManagerInterface $manager) : Response
    {   
        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);
        
        
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $mark->setUser($this->getUser())
                ->setRecipe($recipe);

            $existingMark = $markRepository->findOneBy([
                'user' => $this->getUser(),
                'recipe' => $recipe
            ]);
            if(!$existingMark){
                $manager->persist($mark);
            }else{
                $existingMark->setMark(
                    $form->getData()->getMark()
                );
                
            };


            $manager->flush();

            $this->addFlash(
                'success',
                'Votre note a bien prise en compte!'
            );
            return $this->redirectToRoute('recipe.show', ['id'=>$recipe->getId()]);
        };
        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'form' =>$form->createView()
        ]);

    }


    //Modifier
    #[IsGranted(new Expression('is_granted("ROLE_USER") and user === subject.getUser()'), subject: 'recipe')]
    #[Route('/recette/edition/{id}', name:'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $manager) : Response
    {
        
        $form = $this->createForm(RecipeType::class, $recipe, ['submit_label' => 'Modifier ma recette OK']);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form -> getData();

            $manager -> persist($recipe);
            $manager -> flush();

            $this->addFlash('success', 'Recette modifié avec succès');

            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_USER") and user === subject.getUser()'), subject: 'recipe')]
    #[Route('/recette/suppression/{id}', name: 'recipe.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Recipe $recipe) : Response
    {   
        foreach($recipe->getMarks() as $mark){
            $manager->remove($mark);
        }
        if(!$recipe){
            $this->addFlash('danger', 'Recette introuvable');
            return $this->redirectToRoute('recipe.index');
        }
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash('success', 'Recette supprimé avec succès');

        return $this->redirectToRoute('recipe.index');
    }
} 
