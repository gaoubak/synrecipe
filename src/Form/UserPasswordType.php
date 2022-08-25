<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;


class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => [
                'attr' =>[
                    'class' => 'form-control',
                ],
                'label' => 'password',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
            ],
            'second_options' => [
                'attr' =>[
                    'class' => 'form-control',
                ],
                'label' => 'password_confirmation',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
            ]
            ])
        ->add('newPassword', PasswordType::class, [
            'label' => 'Nouveau mot de passe ',
            'attr' => [
                'class' => 'form-control',
            ],
            'label_attr' => [
                'class' => 'form-label mt-4',
            ],
            'constraints' => [
                new Assert\NotBlank()
            ]
        ])
        ->add('submit', SubmitType::class,[
            'attr' => [
                'class' => 'btn btn-primary mt-4',
            ],
            ]);
    }
}