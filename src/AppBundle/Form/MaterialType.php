<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MaterialType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('nombre',EntityType::class, array(
                    'class'=>'AppBundle\Entity\MaterialNombre', 
                    'required' => true
                ))
                // ->add('referencia')
//                ->add('imagen')
                ->add('presentacion', TextType::class, ['label' => 'Referencia', 'required' => true])
                ->add('categoria',EntityType::class, array(
                    'class'=>'AppBundle\Entity\MaterialCategoria',
                    'required' => true
                ))
                ->add('marca')
                ->add('unidad', TextType::class, ['required' => true])
                ->add('color',EntityType::class, array(
                    'class'=>'AppBundle\Entity\MaterialColor',
                ))
                ->add('medida',EntityType::class, array(
                    'class'=>'AppBundle\Entity\MaterialMedida',
                ))
                ->add('descripcion', TextareaType::class, array(
                    'label' => 'Descripción',
                    'required' => false
                ))
                ->add('foto', VichImageType::class, [
                    'required' => false,
                    'allow_delete' => true,
                    'download_link' => false,
                    'label' => 'Imagen'
                ])
//                ->add('fechaCreacion')
//                ->add('fechaActualizacion')
//                ->add('estado')
//                ->add('usuarioCreacion')
                ;
                
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Material'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_material';
    }


}
