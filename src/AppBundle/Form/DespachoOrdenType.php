<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Security\Core\Security;

class DespachoOrdenType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();
        $builder->add('clienteId')
        ->add('clienteTipo', ChoiceType::class, [
            'choices'  => [
                'DETAL' => 'DETAL',
                'MAYORISTA' => 'MAYORISTA',
            ],
        ])
        ->add('direccionEnvio')
        ->add('tipoPago', ChoiceType::class, [
            'choices'  => [
                'PAYPAL' => 'PAYPAL',
                'TRANSFERENCIA' => 'TRANSFERENCIA',
                'AUTORIZE' => 'AUTORIZE',
                'MONEY ORDER' => 'MONEY ORDER',
                'ZELLE' => 'ZELLE',
                'TARJETA DEBITO O CREDITO' => 'TARJETA DEBITO O CREDITO',
            ],
        ])
        ->add('costoEnvio')
        ->add('total')
        ->add('notas');
        
        if(in_array("ROLE_SUPER_ADMIN",$user->getRoles()) || in_array("ROLE_ADMIN_VENTAS",$user->getRoles())){
            $builder
            ->add('statusPago', ChoiceType::class, [
                'label' => 'Estado de Pago',
                'choices'  => [
                    'Por Pagar' => 1,
                    'Pagado' => 2,
                    'Abonado' => 3,
                ],
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('statusOrden', ChoiceType::class, [
                'label' => 'Estatus Orden',
                'choices'  => [
                    'Por despachar' => 1,
                    'Despachado' => 2
                ],
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('fechaPago', DateTimeType::class, [
                'label' => 'Fecha de Pago Final',
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'form-control-plaintext',
                    'readonly' => true,
                    'placeholder' => 'Se establece automáticamente al completar el pago'
                ]
            ])
            ->add('fechaDespacho', DateTimeType::class, [
                'label' => 'Fecha de Despacho',
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'form-control-plaintext',
                    'readonly' => true,
                    'placeholder' => 'Se establece al despachar la orden'
                ]
            ])
            ->add('abono1', NumberType::class, [
                'label' => 'Primer Abono',
                'required' => false,
                'attr' => [
                    'class' => 'form-control-plaintext',
                    'readonly' => true,
                    'step' => '0.01',
                    'placeholder' => 'No registrado'
                ]
            ])
            ->add('fechaAbono1', DateTimeType::class, [
                'label' => 'Fecha Primer Abono',
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'form-control-plaintext',
                    'readonly' => true,
                    'placeholder' => 'No registrado'
                ]
            ])
            ->add('abono2', NumberType::class, [
                'label' => 'Segundo Abono',
                'required' => false,
                'attr' => [
                    'class' => 'form-control-plaintext',
                    'readonly' => true,
                    'step' => '0.01',
                    'placeholder' => 'No registrado'
                ]
            ])
            ->add('fechaAbono2', DateTimeType::class, [
                'label' => 'Fecha Segundo Abono',
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'form-control-plaintext',
                    'readonly' => true,
                    'placeholder' => 'No registrado'
                ]
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\DespachoOrden'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_despachoorden';
    }
}