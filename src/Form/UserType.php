<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password', PasswordType::class, [
                'mapped' => false 
                ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                'ROLE_ADMIN' => "ROLE_ADMIN",
                'ROLE_USER' => "ROLE_USER",

                ],
                'multiple' => true,
                'expanded' => false
                ])
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('Enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
