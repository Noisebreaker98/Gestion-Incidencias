<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Por favor, introduce tu correo electrónico',
                ]),
                new Email([
                    'message' => 'El correo electrónico {{ value }} no es válido.',
                ]),
            ],
        ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor introduce una contraseña',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'La contraseña debe tener al menos {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('nombre', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, introduce tu nombre',
                    ]),
                ],
            ])
            ->add('apellidos', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, introduce tus apellidos',
                    ]),
                ],
            ])
            ->add('telefono', IntegerType::class, [
                'constraints' => [
                    new NotNull([
                        'message' => 'Por favor, introduce un teléfono',
                    ]),
                    new Regex([
                        'pattern' => '/^\d{9}$/',
                        'message' => 'El número de teléfono debe tener 9 dígitos.',
                    ]),
                ],
            ])
            ->add('foto', FileType::class,[
                'required' => false,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
