<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('firstname', TextType::class,[
                'label' => 'Prénom',
                'constraints' => new Length(['min' => 2,'max' => 30]),
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre prénom...'
                ]
            ])
            ->add('lastname', TextType::class,[
                'label' => 'Nom',
                'constraints' => new Length(['min' => 2,'max' => 60]),
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre nom...'
                ]
            ])
            ->add('email', EmailType::class,[
                'label' => 'email',
                'constraints' => new Length(['min' => 2,'max' => 80]),
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre email...'
                ] 
            ])
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et sa confirmation doivent être identique',
                'label' => 'Mot de passe',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Veuillez saisir votre mot de passe'
                    ]
                                    
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                        'placeholder' => 'Veuillez répéter votre mot de passe'
                    ]],
                ])
            ->add('agreeTerms', CheckboxType::class, [
                    'mapped' => false,
                    'label_html' => true,
                    'label' => "Accepter <a href='/mentions-legales' target='blank'>les conditions générales de vente </a>",
                    'attr' => [
                        'class' => 'form-check form-check-input'
                    ],
                    'constraints' => [
                        new IsTrue([
                            'message' => 'Veuillez accepter les conditions générales de vente'
                        ])
                    ]
                ])
           
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
