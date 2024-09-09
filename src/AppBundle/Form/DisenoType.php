<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Form\DisenoMaterialType;
use AppBundle\Entity\DisenoOrden;
use AppBundle\Form\ProcesoNombreType;
use AppBundle\Form\DisenoImagenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DisenoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('referencia')
                ->add('nombre')
                ->add('categoria', ChoiceType::class, [
                    'choices'  => [
                        'PANTALONES' => 'PANTALONES',
                        'JEANS' => 'JEANS',
                        'CAMISAS' => 'CAMISAS',
                        'BODYS' => 'BODYS',
                        'SHORTS' => 'SHORTS',
                        'FALDAS' => 'FALDAS',
                        'BRAGAS' => 'BRAGAS',
                        'ENTERIZOS' => 'ENTERIZOS',
                        'TOREROS' => 'TOREROS',
                        'CAPRIS' => 'CAPRIS',
                        'TOBILLEROS' => 'TOBILLEROS',
                        'CHAQUETAS' => 'CHAQUETAS',
                    ],
                ])
                ->add('talla',ChoiceType::class, [
                    'choices'  => [
                        '6' => '6', 
                        '8' => '8',
                        '10' => '10',
                        '12' => '12',
                        '14' => '14',
                        '16' => '16',
                        '18' => '18',
                        '20' => '20',
                        '22' => '22',
                        'XS' => 'XS',
                        'S' => 'S',
                        'M' => 'M',
                        'L' => 'L',
                        'XL' => 'XL',
                        'XXL' => 'XXL',
                    ],
                ])
                ->add('costoCorte')
                ->add('orden')
                ->add('materiales', CollectionType::class, [
                    'entry_type' => DisenoMaterialType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
                ->add('imagenes', CollectionType::class, [
                    'entry_type' => DisenoImagenType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
                ->add('procesos', CollectionType::class, [
                    'entry_type' => ProcesoNombreType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
//                ->add('usuarioCreacion')
                ; 
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Diseno'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_diseno';
    }


}
