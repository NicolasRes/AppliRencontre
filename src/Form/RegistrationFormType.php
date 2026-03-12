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
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'constraints' => [
                    new NotBlank(['message' => 'Le pseudo est obligatoire']),
                    new Length([
                        'min' => 3,
                        'max' => 30,
                        'minMessage' => 'Le pseudo doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'Le pseudo ne peut pas dépasser {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_\-\.]+$/',
                        'message' => 'Le pseudo ne peut contenir que des lettres, chiffres, tirets, points et underscores',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire']),
                    new Email(['message' => 'Veuillez entrer un email valide']),
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Entrez un mot de passe']),
                    new Length([
                        'min' => 8,
                        'max' => 100,
                        'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'Le mot de passe ne peut pas dépasser {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'Le mot de passe doit contenir au moins une majuscule',
                    ]),
                    new Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'Le mot de passe doit contenir au moins un chiffre',
                    ]),
                ],
            ])
            ->add('imageIdentite', FileType::class, [
                'label' => 'Photo d\'identité (JPG/PNG)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Une photo d\'identité est obligatoire']),
                    new File([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 2 Mo',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Image JPG ou PNG uniquement',
                    ]),
                ],
            ])
            // --- CHAMPS POUR LE PROFIL (Non mappés sur Utilisateur) ---
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire']),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le prénom doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-\']+$/u',
                        'message' => 'Le prénom ne peut contenir que des lettres, espaces, tirets et apostrophes',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire']),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-\']+$/u',
                        'message' => 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes',
                    ]),
                ],
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Âge',
                'mapped' => false,
                'attr' => ['min' => 18, 'max' => 120],
                'constraints' => [
                    new NotBlank(['message' => 'L\'âge est obligatoire']),
                    new Range([
                        'min' => 18,
                        'max' => 120,
                        'notInRangeMessage' => 'L\'âge doit être compris entre {{ min }} et {{ max }} ans',
                    ]),
                ],
            ])
            ->add('genre', ChoiceType::class, [
                'label' => 'Sexe',
                'mapped' => false,
                'choices' => [
                    'Homme' => 'M',
                    'Femme' => 'F',
                    'Autre' => 'A',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un genre']),
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