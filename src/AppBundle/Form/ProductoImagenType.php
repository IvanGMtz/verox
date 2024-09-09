<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProductoImagenType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('producto')
        ->add('orden', IntegerType::class, [
            'required' => true,
            'label' => 'Orden',
            'attr' => [
                'placeholder' => 'Orden...'
            ]
        ])
        ->add('color',TextType::class,[
            'attr' => [
                'placeholder' => 'Color'
            ]
        ])
        ->add('foto', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Imagen'
        ]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ProductoImagen'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_productoimagen';
    }


}
