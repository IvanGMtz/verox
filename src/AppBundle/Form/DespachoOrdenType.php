<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
        //->add('statusPago')
        //->add('statusOrden')
        ->add('costoEnvio')
        ->add('total')
        ->add('notas');
        if(in_array("ROLE_SUPER_ADMIN",$user->getRoles()) || in_array("ROLE_ADMIN_VENTAS",$user->getRoles())){
            $builder->add('fechaCreacion')
            ->add('statusPago',NumberType::class, [
                'label' => 'Estatus Pago (1= Por pagar // 2= Pagado)',
                'required' => true,
            ])
            ->add('statusOrden',NumberType::class, [
                'label' => 'Estatus Orden (1= Por despachar // 2= Despachado)',
                'required' => true,
            ]);
        }
    }/**
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
