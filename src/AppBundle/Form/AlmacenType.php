<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class AlmacenType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('nombre', null, [
                    "label" => "Nombre",
                    "required" => true
                ])
                ->add('direccion', null, [
                    "label" => "Dirección",
                    "required" => true
                ])
                ->add('telefono1', null, [
                    "label" => "Teléfono 1",
                    "required" => false
                ])
                ->add('telefono2', null, [
                    "label" => "Teléfono 2",
                    "required" => false
                ])
                ->add('email', EmailType::class, [
                    "label" => "Email",
                    "required" => false
                ])
                ->add('nombreContacto', null, [
                    "label" => "Nombre contacto",
                    "required" => false
                ])
                ->add('telefonoContacto', null, [
                    "label" => "Teléfono contacto",
                    "required" => false
                ])
//                ->add('fechaCreacion')
//                ->add('estado')
                ->add('ciudad', null, [
                    "label" => "Ciudad",
                    "required" => false
                ])
                ->add('pais', null, [
                    "label" => "País",
                    "required" => false
                ])
//                ->add('usuarioCreacion')
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Almacen'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_almacen';
    }


}
