<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class DisenoImagenType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//                ->add('imagen')
                ->add('orden', IntegerType::class, [
                    'required' => true,
                    'label' => 'Orden',
                    'attr' => [
                        'placeholder' => 'Orden...'
                    ]
                ])
                ->add('foto', VichImageType::class, [
                    'required' => false,
                    'allow_delete' => false,
                    'download_link' => false,
                    'label' => 'Imagen'
                ])
//                ->add('fechaActualizacion')
//                ->add('estado')
//                ->add('diseno')
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\DisenoImagen'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_disenoimagen';
    }


}
