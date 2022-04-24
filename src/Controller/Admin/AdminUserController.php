<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserAdminType;
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

class AdminUserController extends AbstractController
{
    /**
     * @Route("admin/users", name="admin_list_user")
     */
    public function listUser(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render("admin/user_list.html.twig", ['users' => $users]);

    }

    /**
     * @Route("admin/update/user/{id}", name="admin_user_update")
     */
    public function userUpdate(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        $id
    ) {


        $user = $userRepository->find($id);

        $userForm = $this->createForm(UserAdminType::class, $user);

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
     * @Route("admin/create/user", name="admin_create_user")
     */
    public function createUser(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        MailerInterface $mailerInterface
    ) {

        $user = new User();

        $userForm = $this->createForm(UserAdminType::class, $user);

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
     * @Route("admin/delete/user/{id}", name="admin_user_delete")
     */
    public function userDelete(
        EntityManagerInterface $entityManagerInterface,
        UserRepository $userRepository,
        $id)
    {

        $user = $userRepository->find($id);

        $entityManagerInterface->remove($user);
        $entityManagerInterface->flush();
    
        return $this->redirectToRoute('admin_list_user');
    }
}
