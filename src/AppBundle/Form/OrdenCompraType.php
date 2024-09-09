<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\OrdenCompraItemType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OrdenCompraType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//                ->add('fechaCreacion')
//                ->add('fechaActualizacion')
//                ->add('fechaAceptacion')
//                ->add('fechaRecibe')
                ->add('proveedor', null, [
                    "label" => "Proveedor",
                    "required" => true
                ])
                ->add('almacenDestino', null, [
                    "label" => "Almacén destino",
                    "required" => false,
                    'choice_attr' => function($choice, $key, $value) {
                        // adds a class like attending_yes, attending_no, etc
                        return ['data-address' => $choice->getDireccion()];
                    }
                ])
                ->add('direccionDestino', null, [
                    "label" => "Dirección destino",
                    "required" => true
                ])
                ->add('items', CollectionType::class, [
                    'entry_type' => OrdenCompraItemType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
                ->add('descripcion', null, [
                    "label" => "Descripción",
                    "required" => false
                ])
                ->add('valor', null, [
                    "label" => false,
                    "required" => true
                ])
                ->add('impuesto', null, [
                    "label" => "Impuesto",
                    "required" => true
                ])
                ->add('valorImpuesto', null, [
                    "label" => "Total impuesto",
                    "required" => true
                ])
                ->add('valorTotal', null, [
                    "label" => false,
                    "required" => true
                ])
                ->add('metodoPago',ChoiceType::class,array(
                    'label'=>false,
                    'choices'=>array(
                        'EFECTIVO'=>'EFECTIVO',
                        'TARJETA DE CRÉDITO'=>'TARJETA DE CRÉDITO',
                        'CONSIGNACIÓN'=>'CONSIGNACIÓN',
                        'GIRO'=>'GIRO',
                        'TRANSFERENCIA'=>'TRANSFERENCIA',
                        'CRÉDITO'=>'CRÉDITO'
                    ),
                    'required' => true
                  )
                )
//                ->add('estado')
//                ->add('usuarioAceptacion')
//                ->add('usuarioActualizacion')
//                ->add('usuarioCreacion')
//                ->add('usuarioRecibe')
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrdenCompra'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_ordencompra';
    }


}
