<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductoInventarioMovimientoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder
            ->add('producto', null, ['disabled' => true])
            ->add('color', null, ['disabled' => true])
            ->add('movimiento', null, ['disabled' => true])
            ->add('cantidad', null, ['disabled' => true])
            ->add('bodega', null, ['disabled' => true])
            ->add('informacion')
            ->add('usuario', null, ['disabled' => true])
            ->add('fecha', null, ['disabled' => true]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ProductoInventarioMovimiento'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_productoinventariomovimiento';
    }


}
