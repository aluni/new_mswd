<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipanteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('nombre')
            ->add('apellidos')
            ->add('sexo')
            ->add('universidad')
            ->add('comoConoce')
            ->add('nacionalidad')
            ->add('observaciones')
            ->add('numeroEntrada')
            ->add('asistido')
            ->add('sorteos')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Participante'
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return 'swd_madridbundle_participante';
    }
}
