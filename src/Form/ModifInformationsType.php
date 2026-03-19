<?php

namespace App\Form;

use App\Entity\Profil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

class ModifInformationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom : ',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom ne doit pas être vide']),
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
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom : ',
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom ne doit pas être vide']),
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
                ]
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Âge : ',
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
                'label' => 'Genre : ',
                'choices' => [
                    'Homme' => 'H',
                    'Femme' => 'F',
                    'Autres' => 'A',
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville : '
            ])
            ->add('presentation', TextareaType::class, [
                'label' => 'Présentation : ',
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La présentation ne doit pas dépasser 500 caractères',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profil::class,
        ]);
    }
}
