<?php

namespace App\Controller;

use DateTime;
use App\Classe\Mail;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        if ($request->get('email')) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $request->get('email')]);
            if ($user) {
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new DateTimeImmutable());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                $url =  $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken(),     
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );


                $mail = new Mail();
                $content = "Bonjour ".$user->getFirstname().",<br>Voici votre email de modification de mot de passe suite à un oubli !<br><br>Veuillez cliquer sur le lien pour <a href='".$url."'>réinitialiser le mot de passe</a>";
                $mail->send($user->getEmail(),$user->getFirstname().' '.$user->getLastName(),'Réinitialiser votre mot de passe - LeBipBip',$content);
            
                $this->addFlash('notice', 'Vous allez recevoir un email de modification, merci pour votre confiance!');
            }else {
                $this->addFlash('notice', 'Cette adresse mail nous est inconnu');
            }
        }
        return $this->render('reset_password/index.html.twig');
    }
    /**
     * @Route("/modifier-mot-de-passe-oublie/{token}", name="update_password")
     */
    public function update(Request $request, $token, UserPasswordHasherInterface $encoder )
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneBy(['token' => $token]);
        if (!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }
        $now = new DateTime();
        if ($now > $reset_password->getCreatedAt()->modify('+ 1 hour')) {
            $this->addFlash('notice', 'Votre délai de modification mot de passe à expiré. Merci de réessayer!');
            return $this->redirectToRoute('reset_password');
        }
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $new_password = $form->get('new_password')->getData();

            $password = $encoder->hashPassword($reset_password->getUser(),$new_password);

            $reset_password->getUser()->setPassword($password);
            $this->entityManager->flush();

            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour !!');
            return $this->redirectToRoute('app_login');
        }


        return $this->render('reset_password/update.html.twig', [
            'form' =>$form->createView()
        ]);

    }
}
