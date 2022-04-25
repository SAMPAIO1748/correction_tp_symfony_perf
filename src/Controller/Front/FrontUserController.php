<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class FrontUserController extends AbstractController
{
   /**
     * @Route("update/user", name="front_user_update")
     */
    public function userUpdate(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasherInterface
    ) {

        $user_connect = $this->getUser();

        $user_email = $user_connect->getUserIdentifier();

        $user = $userRepository->findOneBy(['email' => $user_email]);

        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $plainPassword = $userForm->get('password')->getData();
            $hashedpasword = $userPasswordHasherInterface->hashPassword($user, $plainPassword);
            $user->setPassword($hashedpasword);

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('list_product');
        }
        return $this->render("front/user_form.html.twig", ['userForm' => $userForm->createView()]);
    }

    /**
     * @Route("create/user", name="create_user")
     */
    public function createUser(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        MailerInterface $mailerInterface
    ) {

        $user = new User();

        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $user->setDateEnregistrement(new \DateTime("NOW"));
           

            $plainPassword = $userForm->get('password')->getData();
            $hashedpasword = $userPasswordHasherInterface->hashPassword($user, $plainPassword);
            $user->setPassword($hashedpasword);

            $email_user = $userForm->get('email')->getData();

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $email = (new Email())
                ->from('test@test.com') 
                ->to($email_user) 
                ->subject('inscription') 
                ->html('<h1>Bienvenue chez nous</h1>'); 

            $mailerInterface->send($email);

            return $this->redirectToRoute('list_product');
        }
        return $this->render("front/user_form.html.twig", ['userForm' => $userForm->createView()]);
    }

    /**
     * @Route("delete/user", name="front_user_delete")
     */
    public function userDelete(
        EntityManagerInterface $entityManagerInterface,
        UserRepository $userRepository)
    {

        $user_connect = $this->getUser();

        $user_email = $user_connect->getUserIdentifier();

        $user = $userRepository->findOneBy(['email' => $user_email]);

        $entityManagerInterface->remove($user);
        $entityManagerInterface->flush();
    
        return $this->redirectToRoute('list_product');
    }
}
