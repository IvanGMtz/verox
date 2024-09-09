<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreBonosType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codigo')
        ->add('valor')
        ->add('fechaVencimiento')
        ->add('estatus',ChoiceType::class, [
            'choices'  => [
                'ACTIVO' => 'ACTIVO',
                'NO DISPONIBLE' => 'NO DISPONIBLE',
                'VENCIDO' => 'VENCIDO',
            ],
        ])
        ->add('clienteTipo',ChoiceType::class, [
            'choices'  => [
                'DETAL' => 'DETAL',
                'MAYORISTA' => 'MAYORISTA',
                'TODOS' => 'Todos'
            ],
        ])
        ->add('freeShipping',ChoiceType::class, [
            'choices'  => [
                'No' => '0',
                'Si' => '1'
            ],
        ])
        ->add('producto')
        ->add('usuario')
        ->add('categoria');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\StoreBonos'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_storebonos';
    }


}
