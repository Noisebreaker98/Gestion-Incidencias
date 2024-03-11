<?php

namespace App\Form;

use App\Entity\Cliente;
use App\Entity\Incidencia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as TypeDateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NuevaIncidenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo')
            ->add('fechaCreacion', TypeDateTimeType::class, [
                'placeholder' => 'Seleccione una fecha y hora',
                'data' => new \DateTime(),
            ])
            ->add('estado', ChoiceType::class, [
                'choices' => [
                    'Iniciada' => 'iniciada', // Valor por defecto
                    'En proceso' => 'en proceso',
                    'Finalizada' => 'finalizada',
                ],
                'placeholder' => 'Seleccione un estado', // Texto de marcador de posición
            ])
            ->add('cliente', EntityType::class, [
                'class' => Cliente::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione el Cliente'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Incidencia::class,
        ]);
    }
}
