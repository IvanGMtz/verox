<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OrdenCompraItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//                ->add('fechaCreacion')
                ->add('cantidad', NumberType::class, [
                    'label' => 'Cantidad',
                    'required' => true,
                    'attr' => [
                        'class' => 'cantidad'
                    ]
                ])
                ->add('referencia', TextType::class, [
                    'label' => 'Referencia',
                    'required' => true,
                    'attr' => [
                        'class' => 'referencia',
                        'readonly' => true,
                    ]
                ])
                ->add('valorUnidad', NumberType::class, [
                    'label' => 'Valor unidad',
                    'required' => true,
                    'attr' => [
                        'class' => 'valor_unidad'
                    ]
                ])
                ->add('valorTotal', NumberType::class, [
                    'label' => 'Valor total',
                    'required' => true,
                    'attr' => [
                        'class' => 'valor_total'
                    ]
                ])
//                ->add('estado')
                ->add('material', null, [
                    "label" => "Material",
                    "required" => true,
                    "attr" => [
                        "class" => "material"
                    ],
                    'choice_attr' => function($choice, $key, $value) {
                        // adds a class like attending_yes, attending_no, etc
                        return ['data-costo' => $choice->getCostoActual()?$choice->getCostoActual():0,
                                'data-unidad' =>$choice->getUnidad(),
                                'data-referencia' => $choice->getPresentacion()
                        ];
                    }
                ])
//                ->add('ordenCompra')
//                ->add('usuarioCreacion')
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrdenCompraItem'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_ordencompraitem';
    }


}
