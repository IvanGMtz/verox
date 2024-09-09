<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EquipoTrabajoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre')
        ->add('area',ChoiceType::class, [
            'choices'  => [
                'DISEÑO' => 'DISEÑO',
                'TRAZO' => 'TRAZO',
                'CORTE' => 'CORTE',
                'BORDADO' => 'BORDADO',
                'CONFECCION' => 'CONFECCION',
                'LAVANDERIA' => 'LAVANDERIA',
                'PRETERMINADOS' => 'PRETERMINADOS',
                'TERMINADOS' => 'TERMINADOS',
                'FOTOS' => 'FOTOS',
                'EMPAQUE' => 'EMPAQUE',
                'ADMINISTRATIVO' => 'ADMINISTRATIVO',
            ],
        ])
        ->add('activo')
        ->add('direccion')
        ->add('telefono')
        ->add('cc');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\EquipoTrabajo'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_equipotrabajo';
    }


}
