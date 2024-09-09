<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductoInventarioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ingresoDetal')
        ->add('egresoDetal')
        ->add('qtyActualDetal')
        ->add('ultimoIngresoD')
        ->add('ultimoEgresoD')
        ->add('ingresoMayorista')
        ->add('egresoMayorista')
        ->add('qtyActualMayorista')
        ->add('ultimoIngresoM')
        ->add('ultimoEgresoM')
        ->add('producto')
        ->add('color')
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ProductoInventario'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_productoinventario';
    }


}
