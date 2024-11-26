<?php

namespace App\Form;

use App\Entity\Institucion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class ParticipanteFiltrosType extends AbstractType {

    /**
     * Constructor del formulario.
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder->add('numero_entrada', TextType::class, [
                    'label' => 'Nº Entrada',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.numero_entrada']])
                ->add('nombre', TextType::class, [
                    'label' => 'Nombre',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.nombre']])
                ->add('sorteo', TextType::class, [
                    'label' => 'Sorteo',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.sorteo']])
                ->add('premiado', ChoiceType::class, [
                    'label' => 'Premiado',
                    'placeholder' => 'Premiado',
                    'required' => false,
                    'choices' => [
                        '1' => 'Sí',
                        '0' => 'No'],
                    'attr' => ['ng-model' => 'filtroext.premiado']])
                ->add('apellidos', TextType::class, [
                    'label' => 'Apellidos',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.apellidos']])
                ->add('como_conoce', TextType::class, [
                    'label' => '¿Cómo nos conoce?',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.como_conoce']])
                ->add('email', TextType::class, [
                    'label' => 'Email',
                    'required' => false,
                    'attr' => ['ng-model' => 'filtro.email']])
                ->add('sexo', ChoiceType::class, [
                    'label' => 'Género',
                    'required' => false,
                    'placeholder' => 'Género',
                    'choices' => [
                        '1' => 'Hombre',
                        '0' => 'Mujer'],
                    'attr' => ['ng-model' => 'filtro.sexo']])
                ->add('validado', ChoiceType::class, [
                    'label' => '¿Validado?',
                    'required' => false,
                    'placeholder' => 'Validado',
                    'choices' => [
                        'true' => 'Sí',
                        'false' => 'No'],
                    'attr' => ['ng-model' => 'filtro.validado']])
                ->add('asistido', ChoiceType::class, [
                    'label' => '¿Asistido?',
                    'required' => false,
                    'placeholder' => '¿Asistido?',
                    'choices' => [
                        'true' => 'Sí',
                        'false' => 'No'],
                    'attr' => ['ng-model' => 'filtro.asistido']])
                ->add('pais', ChoiceType::class, [
                    'label' => 'País',
                    'required' => false,
                    'placeholder' => 'País',
                    'choices' => $options['paises'],
                    'attr' => ['ng-model' => 'filtro.nacionalidad']])
                ->add('institucion', EntityType::class, [
                    'label' => 'Institución',
                    'class' => Institucion::class,
                    'placeholder' => 'Institución',
                    'attr' => ['ng-model' => 'filtroext.institucionId']]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'paises' => [],
        ]);
    }

    /**
     * Función que define el nombre del formulario.
     * 
     * @return string
     */
    public function getName(): string {
        return 'participantesfiltrosForm';
    }

}
