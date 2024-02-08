<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("name", TextType::class, [
                'constraints' => [
                    new NotBlank(message: "Name must not be empty"),
                    new Length(max: "150", maxMessage: "Name must not be longer than 256")
                ],
                'required' => true
            ])
            ->add("email", TextType::class, [
                'constraints' => [
                    new NotBlank(message: "Email must not be empty"),
                    new Length(max: "256", maxMessage: "Email must not be longer than 256"),
                    new Regex(pattern: '/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]+$/', message: "Incorrect email")
                ],
                'required' => true
            ])
            ->add("password", RepeatedType::class, [
                'constraints' => [
                    new NotBlank(message: "Password must not be empty"),
                    new Length(min: "5", max: "256", maxMessage: "Email must not be longer than 256")
                ],
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add("send", SubmitType::class, ["label" => "Send"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity(fields: ['email'], message: 'This email is already taken'),
            ],
        ]);
    }
}
