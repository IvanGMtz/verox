<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Material;
use AppBundle\Entity\ProcesoNombre;

class DisenoMaterialType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('material', EntityType::class, [
                    'class' => Material::class,
                    'label' => 'Material',
                    'required' => true,
                    'choice_attr' => function($choice, $key, $value) {
                        return ['data-imagen' => $choice->getImagen(), 'data-unidad' => $choice->getUnidad(),'data-costo' => $choice->getCostoActual()];
                    },
                ])
                ->add('cantidad')
                ->add('proceso', EntityType::class, [
                    'class' => ProcesoNombre::class,
                    'label' => 'Proceso',
                    'required' => true,
                    'choice_attr' => function($choice, $key, $value) {
                        return ['data-nombre' => $choice->getNombre()];
                    },
                ])
//                ->add('diseno')
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\DisenoMaterial'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_disenomaterial';
    }


}
