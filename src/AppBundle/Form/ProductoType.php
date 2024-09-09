<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\ProductoImagenType;
use AppBundle\Form\ProductoComplementoType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProductoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */ 
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre')
        ->add('precioMayorista')
        ->add('precioDetal')
        ->add('referencia')
        ->add('categoria')
        ->add('estado', ChoiceType::class, [
            'choices'  => [
                'DISPONIBLE' => 'DISPONIBLE',
                'INACTIVO' => 'INACTIVO',
            ],
        ])
        ->add('imagenes', CollectionType::class, [
            'entry_type' => ProductoImagenType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ])
        ->add('tallas', CollectionType::class, [
            'entry_type' => ProductoTallaType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ])
        ->add('colores', CollectionType::class, [
            'entry_type' => ProductoColorType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'required' => true
        ])
        ->add('complementos', CollectionType::class, [
            'entry_type' => ProductoComplementoType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ])
        ->add('etiqueta',ChoiceType::class, [
            'choices'  => [
                'NEW' => 'NEW',
                'SALES' => 'SALES',
                'NINGUNA' => 'NINGUNA',
            ],
        ])
        ->add('marca',ChoiceType::class, [
            'choices'  => [
                'VEROX' => 'VEROX',
                'KIWI' => 'KIWI',
            ],
        ])
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Producto'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_producto';
    }


}
