<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreUsuariosType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email')
        ->add('nombre')
        ->add('apellidos')
        ->add('idn')
        ->add('telefono')
        ->add('birthday')
        ->add('direccion')
        ->add('estado')
        ->add('tipo',ChoiceType::class, [
            'choices'  => [
                'DETAL' => 'DETAL',
                'MAYORISTA' => 'MAYORISTA',
            ],
        ])
        ->add('asesor',ChoiceType::class, [
            'choices'  => [
                'ANDREA CHAVEZ' => 'ANDREA CHAVEZ',
                'LADY BOHORQUEZ' => 'LADY BOHORQUEZ',
                'MAYERLIN FINOL' => 'MAYERLIN FINOL',
                'VALENTINA HERNANDEZ' => 'VALENTINA HERNANDEZ',
                'ESTEFANIA PARRA' => 'ESTEFANIA PARRA',
                'ALEJANDRA MONCADA' => 'ALEJANDRA MONCADA',
                'DORIS QUINTERO' => 'DORIS QUINTERO',
                'SANDRA DURAN' => 'SANDRA DURAN',
                'YULI PEÑA' => 'YULI PEÑA',
                'LIZ MONTAÑEZ' => 'LIZ MONTAÑEZ',
                'CESAR GOMEZ' => 'CESAR GOMEZ',
                'BEATRIZ LOPEZ' => 'BEATRIZ LOPEZ',
                'YULIANA AMARIS' => 'YULIANA AMARIS',
                'JULIANA CACERES' => 'JULIANA CACERES',
                'LUISA VESGA' => 'LUISA VESGA',
                'MANUEL GONZALEZ' => 'MANUEL GONZALEZ',
                'MAYERLY GARCIA' => 'MAYERLY GARCIA',
                'JOAN ROJAS' => 'JOAN ROJAS',
                'MARIA CECILIA REYES' => 'MARIA CECILIA REYES',
                'MARIA PAULA HERNANDEZ' => 'MARIA PAULA HERNANDEZ',
                'ROCIO SALAZAR' => 'ROCIO SALAZAR',
                'DANIELA MORENO' => 'DANIELA MORENO',
                'ROBERTO CARLOS LOPEZ' => 'ROBERTO CARLOS LOPEZ',
                'MATEO CASTRO' => 'MATEO CASTRO'
            ],
        ]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\StoreUsuarios'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_storeusuarios';
    }


}
