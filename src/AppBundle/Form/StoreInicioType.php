<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreInicioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('foto1', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Imagen Fondo'
        ])
        ->add('foto2', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Imagen VRX'
        ])
        ->add('foto3', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Imagen KIWI'
        ])
        ->add('fuente')
        ->add('hexFuente',ColorType::class)
        ->add('hexModalBody',ColorType::class)
        ->add('hexModalHeader',ColorType::class);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\StoreInicio'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_storeinicio';
    }


}
