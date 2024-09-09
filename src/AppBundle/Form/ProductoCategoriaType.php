<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductoCategoriaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombreEs', null, [
            'label' => 'Nombre',
            'required' => true
        ])
        ->add('nombreEn', null, [
            'label' => 'Descripción',
            'required' => false
        ])
        ->add('principal', ChoiceType::class,[
        'choices'=>[
            "Si"=>"Si",
            "No"=>"No"
        ]])
        ->add('verox', ChoiceType::class,[
        'choices'=>[
            "Si"=>"Si",
            "No"=>"No"
        ]])
        ->add('kiwi', ChoiceType::class,[
        'choices'=>[
            "Si"=>"Si",
            "No"=>"No"
        ]])
        ->add('orden')
        ->add('foto', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Imagen VRX'
        ])
        ->add('foto2', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Imagen KIWI'
        ]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ProductoCategoria'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_productocategoria';
    }


}
