<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\InventarioOrdenItemType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InventarioOrdenType extends AbstractType
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
//                ->add('ref1')
//                ->add('ref2')
//                ->add('ref3')
                ->add('descripcion', null, [
                    "label" => "Descripción",
                    "required" => false,
                    'attr' => [
                        "rows" => 1
                    ]
                ])
                ->add('departamentoSolicita', null, [
                    "label" => "Departamento solicitud",
                    "required" => false,
                    'attr' => [
                        'readonly' => 'readonly'
                    ]
                ])
                ->add('items', CollectionType::class, [
                    'entry_type' => InventarioOrdenItemType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
//                ->add('estado')
//                ->add('tienePendientes')
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
            'data_class' => 'AppBundle\Entity\InventarioOrden'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_inventarioorden';
    }


}
