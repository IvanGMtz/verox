<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class SupplierInvoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invoiceNumber', TextType::class, [
                'label' => 'Número de Factura',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'El número de factura es obligatorio'])
                ]
            ])
            ->add('internalReference', TextType::class, [
                'label' => 'Referencia Interna',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('proveedor', EntityType::class, [
                'class' => 'AppBundle:Proveedor',
                'choice_label' => 'nombre',
                'label' => 'Proveedor',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Seleccione un proveedor',
                'constraints' => [
                    new NotBlank(['message' => 'Debe seleccionar un proveedor'])
                ]
            ])
            ->add('issueDate', DateType::class, [
                'label' => 'Fecha de Emisión',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'La fecha de emisión es obligatoria'])
                ]
            ])
            ->add('dueDate', DateType::class, [
                'label' => 'Fecha de Vencimiento',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('currency', ChoiceType::class, [
                'label' => 'Moneda',
                'choices' => [
                    'Pesos Colombianos (COP)' => 'COP',
                    'Dólares Americanos (USD)' => 'USD',
                    'Euros (EUR)' => 'EUR'
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('exchangeRate', MoneyType::class, [
                'label' => 'Tasa de Cambio',
                'currency' => false,
                'attr' => ['class' => 'form-control', 'step' => '0.0001'],
                'scale' => 4
            ])
            ->add('taxAmount', MoneyType::class, [
                'label' => 'Impuestos',
                'currency' => false,
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('logisticCosts', MoneyType::class, [
                'label' => 'Costos Logísticos',
                'currency' => false,
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('attachment', FileType::class, [
                'label' => 'Archivo Adjunto (PDF/Imagen)',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control-file'],
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'application/pdf',
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Por favor suba un archivo PDF o imagen válido',
                    ])
                ]
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notas',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('items', CollectionType::class, [
                'entry_type' => SupplierInvoiceItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'attr' => ['class' => 'items-collection']
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SupplierInvoice'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_supplierinvoice';
    }
}