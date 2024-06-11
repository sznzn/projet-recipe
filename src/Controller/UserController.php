<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[IsGranted(new Expression('is_granted("ROLE_USER") and user === subject'), subject: 'user')]
    #[Route('/utilisation/edition/{id}', name: 'user.edit')]
    /**
     * This controller allow us to 
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
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
            if($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())){

                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont bien été modifiée'
                );
                return $this->redirectToRoute('recipe.index');
            }else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné incorrect'
                );
            }

        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/utilisation/editionMDP/{id}', 'user.edit.mdp', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_USER") and user === subject'), subject: 'user')]
    public function editPassword(User $user, Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager) : Response
    {
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($hasher->isPasswordValid($user, $form->getData()['plainPassword']))
            {
                $user->setPassword(
                    
                        $hasher->hashPassword(
                            $user,
                            $form->getData()['newPassword']
                        
                    )
                );
                
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifié.'
                );
                
                return $this->redirectToRoute('recipe.index');
            
            }else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné incorrect'
                );
            }
        }
        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
