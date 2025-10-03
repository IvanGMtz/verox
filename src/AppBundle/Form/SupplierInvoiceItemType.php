<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;

class SupplierInvoiceItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', ChoiceType::class, [
                'label' => 'Producto',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control product-select2',
                    'data-placeholder' => 'Buscar producto...'
                ],
                'placeholder' => 'Seleccione un producto',
                'required' => false
            ])
            ->add('productoId', HiddenType::class, [
                'attr' => ['class' => 'producto-id']
            ])
            ->add('productoTallaId', HiddenType::class, [
                'attr' => ['class' => 'talla-id']
            ])
            ->add('productoColorId', HiddenType::class, [
                'attr' => ['class' => 'color-id']
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Cantidad',
                'attr' => [
                    'class' => 'form-control item-quantity',
                    'step' => '0.01',
                    'min' => '0.01'
                ],
                'scale' => 2,
                'constraints' => [
                    new NotBlank(['message' => 'La cantidad es obligatoria']),
                    new GreaterThan(['value' => 0, 'message' => 'La cantidad debe ser mayor a 0'])
                ]
            ])
            ->add('unitPrice', MoneyType::class, [
                'label' => 'Precio Unitario',
                'currency' => false,
                'attr' => [
                    'class' => 'form-control item-unit-price',
                    'step' => '0.0001'
                ],
                'scale' => 4,
                'constraints' => [
                    new NotBlank(['message' => 'El precio unitario es obligatorio']),
                    new GreaterThan(['value' => 0, 'message' => 'El precio debe ser mayor a 0'])
                ]
            ])
            ->add('totalPrice', MoneyType::class, [
                'label' => 'Total',
                'currency' => false,
                'attr' => [
                    'class' => 'form-control item-total-price',
                    'readonly' => true
                ],
                'scale' => 2
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notas',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2
                ]
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SupplierInvoiceItem'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_supplierinvoiceitem';
    }
}