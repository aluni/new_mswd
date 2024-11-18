<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class ParticipanteFiltrosType extends AbstractType {

    protected $paises;

    public function __construct($paises) {
        $this->paises = $paises;
    }

    /**
     * Constructor del formulario.
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('numero_entrada', 'text', [
                    'label' => 'Nº Entrada',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.numero_entrada']])
                ->add('nombre', 'text', [
                    'label' => 'Nombre',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.nombre']])
                ->add('sorteo', 'text', [
                    'label' => 'Sorteo',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.sorteo']])
                ->add('premiado', 'choice', [
                    'label' => 'Premiado',
                    'empty_value' => 'Premiado',
                    'required' => false,
                    'choices' => [
                        '1' => 'Sí',
                        '0' => 'No'],
                    'attr' => ['ng-model' => 'filtroext.premiado']])
                ->add('apellidos', 'text', [
                    'label' => 'Apellidos',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.apellidos']])
                ->add('como_conoce', 'text', [
                    'label' => '¿Cómo nos conoce?',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.como_conoce']])
                ->add('email', 'text', [
                    'label' => 'Email',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.email']])
                ->add('sexo', 'choice', [
                    'label' => 'Género',
                    'required' => false,
                    'empty_value' => 'Género',
                    'choices' => [
                        '1' => 'Hombre',
                        '0' => 'Mujer'],
                    'attr' => ['ng-model' => 'filtro.sexo']])
                ->add('validado', 'choice', [
                    'label' => '¿Validado?',
                    'required' => false,
                    'empty_value' => 'Validado',
                    'choices' => [
                        'true' => 'Sí',
                        'false' => 'No'],
                    'attr' => ['ng-model' => 'filtro.validado']])
                ->add('asistido', 'choice', [
                    'label' => '¿Asistido?',
                    'required' => false,
                    'empty_value' => '¿Asistido?',
                    'choices' => [
                        'true' => 'Sí',
                        'false' => 'No'],
                    'attr' => ['ng-model' => 'filtro.asistido']])
                ->add('pais', 'choice', [
                    'label' => 'País',
                    'required' => false,
                    'empty_value' => 'País',
                    'choices' => $this->paises,
                    'attr' => ['ng-model' => 'filtro.nacionalidad']])
                ->add('institucion', 'entity', [
                    'label' => 'Institución',
                    'class' => 'SWDMadridBundle:Institucion',
                    'placeholder' => 'Institución',
                    'attr' => ['ng-model' => 'filtroext.institucionId']]);
    }

    /**
     * Función que define el nombre del formulario.
     * 
     * @return string
     */
    public function getName() {
        return 'participantesfiltrosForm';
    }

}
