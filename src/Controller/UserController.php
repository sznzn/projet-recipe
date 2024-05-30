<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/utilisation/edition/{id}', name: 'user.edit')]
    /**
     * This controller allow us to 
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(User $user, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('security.login');
        }
        if($this->getUser() !== $user){
            return $this->redirectToRoute('recipe.index');
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations de votre compte ont bien été modifiée'
            );
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
