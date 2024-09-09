<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreTiendaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('fuenteNavbar')
        ->add('nombre')
        ->add('hexFuenteNavbar',ColorType::class)
        ->add('hexFondoNavbar',ColorType::class)
        ->add('whatsappMainColor',ColorType::class)
        ->add('whatsappTextColor',ColorType::class)
        ->add('foto', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Logo Navbar'
        ])
        ->add('foto2', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Imagen Mayoristas 1'
        ])
        ->add('foto3', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Imagen Mayoristas 2'
        ])
        ->add('foto4', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Imagen Mayoristas 3'
        ])
        ->add('foto5', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Imagen Mayoristas 4'
        ])
        ->add('foto6', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Imagen Mayoristas 5'
        ])
        ->add('imagenesSlider', CollectionType::class, [
            'entry_type' => StoreTiendaSliderType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\StoreTienda'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_storetienda';
    }


}
