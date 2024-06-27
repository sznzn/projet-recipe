<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Mailer\MailerInterface;



class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.index')]
    public function index(
                        Request $request, 
                        EntityManagerInterface $manager,
                        // MailerInterface $mailer
                        MailService $mailService
                        ): Response
    {
        $contact = new Contact();

        if($this->getUser()){
            $contact->setFullName($this->getUser()->getFullName())
                    ->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $contact = $form->getData();

            $manager->persist($contact);
            $manager->flush();


            //email avec service 
            $mailService->sendEmail(
                $contact->getEmail(),
                $contact->getSubject(),
                'emails/contact.html.twig',
                ['contact' => $contact]
            );




/*emaii sans service */
            // $email = (new TemplatedEmail())
            // ->from($contact->getEmail())
            // ->to('admin@easyrecipe.com')
            // ->subject($contact->getSubject())
            // ->text('Sending emails is fun again!')
            // ->htmlTemplate('emails/contact.html.twig')
            // ->context([
            //     'contact' => $contact,
            // ]);
                
            
            // $mailer->send($email);


            $this->addFlash('success', 'Votre message a été envoyé');

            return $this->redirectToRoute('contact.index');

        }

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
