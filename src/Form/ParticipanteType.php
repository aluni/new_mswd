<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParticipanteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Participante'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'swd_madridbundle_participante';
    }
}
