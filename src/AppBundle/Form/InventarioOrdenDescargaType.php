<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\InventarioCosto;

class InventarioOrdenDescargaType extends AbstractType
{
//    private $em;
//    private $almacenes;
//    
//    /**
//     * @param EntityManagerInterface $em
//     */
//    public function __construct(EntityManagerInterface $em)
//    {
//        $this->em = $em;
//        $this->almacenes = $em->getRepository('AppBundle:Almacen')->findAll();
//    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $blocked = $options['blocked'];
        $material = $options['material'];
        
        if($blocked){
          $builder
                ->add('cantidad', NumberType::class, [
                    'label' => 'Cantidad',
                    'required' => true,
                ])
                ;
        }else{ 
          $builder
                ->add('cantidad', NumberType::class, [
                    'label' => 'Cantidad',
                    'required' => true,
                ])
//                ->add('anotaciones', null, [
//                    'label' => 'Anotaciones',
//                    'required' => true
//                ])
                ;
        }
        $builder
//                ->add('cantidad')
//                ->add('valorUnitario')
//                ->add('valorTotal')
//                ->add('fechaCreacion')
//                ->add('estado')
//                ->add('almacen', EntityType::class, array(
//                    'class' => Almacen::class,
//                    'label' => 'Almacen',
//                    'required' => true,
//                    'choices' => $this->almacenes
//                ))
//                ->add('almacenZona')
                ->add('almacenZonaInventario', EntityType::class, array(
                    'class' => InventarioCosto::class,
                    'label' => 'Existencias en Inventario',
                    'required' => true,
                    'query_builder' => function (EntityRepository $er) use ($material) {
                        return $er->createQueryBuilder('u')
                            ->where('u.material = :material')
                            ->andWhere('u.cantidadActual > 0')
                            ->setParameter('material', $material)
                            ->orderBy('u.zona', 'DESC')
                            ;
                    },
                ))
//                ->add('inventarioOrden')
//                ->add('inventarioOrdenItem')
//                ->add('material')
//                ->add('usuarioCreacion')
                ;
        
//        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
//        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }
    
//    protected function addElements(FormInterface $form, $almacen = null) {
//        $zonas = array();
//        if ($almacen) {
//          $zonas = $this->em->getRepository('AppBundle:AlmacenZona')->findBy(['almacen' => $almacen]);
//        }
//        $form->add('almacenZona', EntityType::class, array(
//            'label' => 'Zona',
//            'required' => true,
//            'choices' => $zonas,
//            'class' => AlmacenZona::class
//        ));
//    }
//    
//    function onPreSubmit(FormEvent $event) {
//      $em = $this->em;
//      $form = $event->getForm();
//      $data = $event->getData();
//      $almacen = $data['almacen'];
//      $this->addElements($form, $almacen);
//    }
//    
//    function onPreSetData(FormEvent $event) {
//        $item = $event->getData();
//        $form = $event->getForm();
//        if($item){
//          $almacen = $item->getAlmacen() ? $user->getAlmacen() : $this->almacenes[0];
//        }else{
//          $almacen = $this->almacenes[0];
//        }
//        $this->addElements($form, $almacen);
//    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\InventarioOrdenDescarga',
            'blocked' => false,
            'material' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_inventarioordendescarga';
    }


}
