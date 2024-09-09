<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OrdenCompraPagoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//                ->add('fechaCreacion')
                ->add('tipoPago',ChoiceType::class,array(
                    'label'=>'Forma de pago',
                    'choices'=>array(
                        'EFECTIVO'=>'EFECTIVO',
                        'TARJETA DE CRÉDITO'=>'TARJETA DE CRÉDITO',
                        'CONSIGNACIÓN'=>'CONSIGNACIÓN',
                        'GIRO'=>'GIRO',
                        'TRANSFERENCIA'=>'TRANSFERENCIA',
                        'CRÉDITO'=>'CRÉDITO',
                    ),
                    'required' => true
                  )
                )
                ->add('referencia', null, [
                    'label' => '# Factura',
                    'required' => true
                ])
                ->add('valor', null, [
                    'label' => 'Valor',
                    'required' => true
                ])
//                ->add('descripcion', null, [
//                    'label' => 'Descripción',
//                    'required' => false
//                ])
//                ->add('estado')
//                ->add('ordenCompra')
//                ->add('usuarioCreacion')
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrdenCompraPago'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_ordencomprapago';
    }


}
