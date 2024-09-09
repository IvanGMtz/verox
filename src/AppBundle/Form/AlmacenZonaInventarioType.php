<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use AppBundle\Entity\Almacen;
use AppBundle\Entity\AlmacenZona;

class AlmacenZonaInventarioType extends AbstractType
{
    private $em;
    private $almacenes;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->almacenes = $em->getRepository('AppBundle:Almacen')->findAll();
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $blocked = $options['blocked'];
        if($blocked){
          $builder
                ->add('cantidadActual', IntegerType::class, [
                    'label' => 'Cantidad',
                    'attr' => [
                        'readonly' => 'readonly'
                    ]
                ])
                ;
        }else{
          $builder
                ->add('cantidadActual', IntegerType::class, [
                    'label' => 'Cantidad',
                    'required' => true
                ])
                ->add('anotaciones', null, [
                    'label' => 'Anotaciones',
                    'required' => true
                ])
                ;
        }
        $builder
//                ->add('ingresoTotal')
//                ->add('egresoTotal')
//                ->add('cantidadActual', null, [
//                    'label' => 'Cantidad',
//                    'attr' => [
//                        'readonly' => 'readonly'
//                    ]
//                ])
//                ->add('fechaUltimoIngreso')
//                ->add('fechaUltimoEgreso')
                ->add('almacen', EntityType::class, array(
                    'class' => Almacen::class,
                    'label' => 'Almacen',
                    'required' => true,
                    'choices' => $this->almacenes
                ))
//                ->add('almacenZona')
//                ->add('material')
                ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    protected function addElements(FormInterface $form, $almacen = null) {
        $zonas = array();
        if ($almacen) {
          $zonas = $this->em->getRepository('AppBundle:AlmacenZona')->findBy(['almacen' => $almacen]);
        }
        $form->add('almacenZona', EntityType::class, array(
            'label' => 'Zona',
            'required' => true,
            'choices' => $zonas,
            'class' => AlmacenZona::class
        ));
    }

    function onPreSubmit(FormEvent $event) {
      $em = $this->em;
      $form = $event->getForm();
      $data = $event->getData();
      $almacen = $data['almacen'];
      $this->addElements($form, $almacen);
    }

    function onPreSetData(FormEvent $event) {
        $item = $event->getData();
        $form = $event->getForm();
        if($item){
          $almacen = $item->getAlmacen() ? $user->getAlmacen() : $this->almacenes[0];
        }else{
          $almacen = $this->almacenes[0];
        }
        $this->addElements($form, $almacen);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AlmacenZonaInventario',
            'blocked' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_almacenzonainventario';
    }


}
