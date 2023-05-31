<?php

namespace App\Form;

use App\Entity\Adress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class AdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Quel nom souahitez vous donner à cette adresse ?',
                'attr' => [
                    'placeholder' => 'Exemple : Domicile, Travail...'
                ]
            ])
            ->add('firstname',TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Veuillez saisir le prénom'
                ]
            ])
            ->add('lastname',TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Veuillez saisir le prénom'
                ]
            ])
            ->add('company',TextType::class, [
                'label' => 'Société',
                'required' => false,
                'attr' => [
                    'placeholder' => '(Facultatif) Veuillez saisir le nom de la société'
                ]
            ])
            ->add('address',TextType::class, [
                'label' => "L'adresse",
                'attr' => [
                    'placeholder' => "Exemple : 8  rue de l'abbé Pierre"
                ]
            ])
            ->add('postal',TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Veuillez saisir le code postal'
                ]
            ])
            ->add('city',TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Veuillez saisir le nom de la ville'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'Veuillez saisir le nom du pays'
                ]
            ])
            ->add('phone',TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Veuillez saisir le numero de téléphone'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider Mon Adresse',
                 'attr' => [
                    'class' => 'btn-info btn-block'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}
