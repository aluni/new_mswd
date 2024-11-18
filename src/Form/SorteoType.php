<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SorteoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nombre', 'text', [
                    'label' => 'Nombre del sorteo',
                    'attr' => ['ng-model' => 'sorteo.nombre']])
                ->add('cantidad', 'number', [
                    'label' => 'Cantidad de premios',
                    'attr' => ['ng-model' => 'sorteo.cantidad']])
                ->add('condicion', 'choice', [
                    'label' => 'Condición para participar',
                    'empty_value' => 'Condición para participar',
                    'choices' => ['entrada' => 'Entrar al evento',
                        'min_checkeos' => 'Visitar al menos 5 stands',
                        'exclusivo' => 'Visitar el stand de la institucion'],
                    'attr' => ['ng-model' => 'sorteo.condicion']])
                ->add('horaInicio', 'text', [
                    'label' => 'Hora Inicio',
                    'attr' => ['ng-model' => 'sorteo.hora_inicio']])
                ->add('horaFin', 'text', [
                    'label' => 'Hora Fin',
                    'attr' => ['ng-model' => 'sorteo.hora_fin']])
                ->add('institucion', 'entity', [
                    'label' => 'Institución',
                    'class' => 'SWDMadridBundle:Institucion',
                    'placeholder' => 'Institución',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('i')
                                ->orderBy('i.nombre', 'ASC');
                    },
                    'attr' => ['ng-model' => 'sorteo.institucion.id']])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Sorteo'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'swd_madridbundle_sorteo';
    }

}
