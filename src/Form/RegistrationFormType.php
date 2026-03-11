<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'constraints' => [new NotBlank(['message' => 'Le pseudo est obligatoire'])]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire']),
                    new Email(['message' => 'Veuillez entrer un email valide']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Entrez un mot de passe']),
                    new Length(['min' => 6, 'minMessage' => 'Minimum {{ limit }} caractères']),
                ],
            ])
            ->add('imageIdentite', FileType::class, [
                'label' => 'Photo d\'identité (JPG/PNG)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Image JPG ou PNG uniquement',
                    ])
                ],
            ])
            // --- CHAMPS POUR LE PROFIL (Non mappés sur Utilisateur) ---
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'mapped' => false,
                'constraints' => [new NotBlank()]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'mapped' => false,
                'constraints' => [new NotBlank()]
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Âge',
                'mapped' => false,
                'attr' => ['min' => 18, 'max' => 120],
                'constraints' => [new NotBlank()]
            ])
            ->add('genre', ChoiceType::class, [
                'label' => 'Sexe',
                'mapped' => false,
                'choices'  => [
                    'Homme' => 'M',
                    'Femme' => 'F',
                    'Autre ?' => 'A',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}