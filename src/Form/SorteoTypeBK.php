<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class SorteoType extends AbstractType {

    /**
     * Constructor del formulario.
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('nombre', 'text', [
                    'label' => 'Nombre del sorteo',
                    'attr' => ['ng-model' => 'sorteo.nombre']])
                ->add('numero', 'text', [
                    'label' => 'Número de premios',
                    'attr' => ['ng-model' => 'sorteo.numero']]);
    }

    /**
     * Función que define el nombre del formulario.
     * 
     * @return string
     */
    public function getName() {
        return 'sorteoForm';
    }

}
