<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordHasherInterface $encoder ): Response
    {
        $notification = null;
        $user = new User;

        $form = $this->createForm( RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $search_email = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if (!$search_email) {
            $password = $encoder->hashPassword($user,$user->getPassword());
            $user->setPassword($password);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $mail = new Mail();
            $content = "Bonjour ".$user->getFirstname()."<br>Bienvenue sur LeBipBip votre commerce le plus proche de vous<br><br>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sapiente optio at dolor qui modi saepe magnam pariatur nemo error. Aspernatur laboriosam facilis perferendis nam nihil, ipsum laudantium rem soluta eius!";
            $mail->send($user->getEmail(),$user->getFirstname(),'Bienvenue sur LeBipBip',$content);
            $notification= 'Votre Inscription a bien été prise en compte, Connectez-vous !';
        }else{ 
                $notification= "Cet email malheureusement existe dejà, retentez votre chance !";
                
            }

           
        }
        return $this->render('register/index.html.twig',[
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
