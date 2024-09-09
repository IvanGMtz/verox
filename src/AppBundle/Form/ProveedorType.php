<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProveedorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('nombre')
                ->add('nit')
                ->add('direccion')
                ->add('telefono1')
                ->add('telefono2')
                ->add('email')
                ->add('nombreContacto')
                ->add('telefonoContacto')
//                ->add('fechaCreacion')
//                ->add('estado')
                ->add('ciudad')
                ->add('pais')
//                ->add('usuarioCreacion')
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Proveedor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_proveedor';
    }


}
