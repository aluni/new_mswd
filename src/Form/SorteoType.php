<?php

namespace App\Form;

use App\Entity\Institucion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SorteoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
                ->add('nombre', TextType::class, [
                    'label' => 'Nombre del sorteo',
                    'attr' => ['ng-model' => 'sorteo.nombre']])
                ->add('cantidad', IntegerType::class, [
                    'label' => 'Cantidad de premios',
                    'attr' => ['ng-model' => 'sorteo.cantidad']])
                ->add('condicion', ChoiceType::class, [
                    'label' => 'Condición para participar',
                    'placeholder' => 'Condición para participar',
                    'choices' => ['entrada' => 'Entrar al evento',
                        'min_checkeos' => 'Visitar al menos 5 stands',
                        'exclusivo' => 'Visitar el stand de la institucion'],
                    'attr' => ['ng-model' => 'sorteo.condicion']])
                ->add('horaInicio', TextType::class, [
                    'label' => 'Hora Inicio',
                    'attr' => ['ng-model' => 'sorteo.hora_inicio']])
                ->add('horaFin', TextType::class, [
                    'label' => 'Hora Fin',
                    'attr' => ['ng-model' => 'sorteo.hora_fin']])
                ->add('institucion', EntityType::class, [
                    'label' => 'Institución',
                    'class' => Institucion::class,
                    'placeholder' => 'Institución',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('i')
                                ->orderBy('i.nombre', 'ASC');
                    },
                    'attr' => ['ng-model' => 'sorteo.institucion.id']])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Sorteo'
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return 'swd_madridbundle_sorteo';
    }

}
