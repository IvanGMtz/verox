<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Entity\Country;
use AppBundle\Entity\City;

class FosUserType extends AbstractType
{
    private $em;
    private $countries;
    
    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->countries = $em->getRepository('AppBundle:Country')->findAll();
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roles = $options['roles'];
        $builder
                ->add('username', TextType::class, array(
                    'label' => 'Nombre de usuario',
                ))
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'Los password no coinciden',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => false,
                    'first_options'  => array('label' => 'Password','attr' => array('autocomplete'=>'off')),
                    'second_options' => array('label' => 'Repita Password','attr' => array('autocomplete'=>'off')),
                ))
                ->add('email', EmailType::class, array(
                    'label' => 'Email',
                    'required' => true
                ))
                ->add('name', TextType::class, array(
                    'label' => 'Nombre',
                    'required' => true
                ))
                ->add('phone', TextType::class, array(
                    'label' => 'Teléfono',
                    'required' => true
                ))
//                ->add('docType', ChoiceType::class, array(
//                    'label'=>'Tipo documento',
//                    'choices' => array(
//                        'CC'=>'CC',
//                        'NIT'=>'NIT',
//                        'RUT'=>'RUT'
//                    ),
//                    'required' => true
//                ))
//                ->add('doc', TextType::class, array(
//                    'label' => 'Número documento',
//                    'required' => true
//                ))
                ->add('birthday', BirthdayType::class, array(
                    'label' => 'Fecha de nacimiento',
                    'required' => false,
                    'widget' => 'single_text',
                ))
                ->add('country', EntityType::class, array(
                    'class' => Country::class,
                    'label' => 'País',
                    'required' => true,
                    'choices' => $this->countries,
                    'attr' => [
                        'class' => 'select2'
                    ]
                ))
                ->add('address', TextType::class, array(
                    'label' => 'Dirección',
                    'required' => false
                ))
//                ->add('balance')
//                ->add('serial')
//                ->add('changePassword')
//                ->add('verified')
//                ->add('wallet', TextType::class, array(
//                    'label' => 'Wallet',
//                    'required' => false
//                ))
//                ->add('parent')
                ->add('roles', ChoiceType::class, array(
                    'label' => 'Rol',
                    'mapped' => true,
                    'expanded' => true,
                    'multiple' => true,
                    'choices'  => $roles
                ))
                ->add('avatar', VichImageType::class, [
                    'required' => false,
                    'allow_delete' => true,
                    'download_link' => false,
                    'label' => 'Imagen de pérfil'
                ])
//                ->add('ccFront', VichImageType::class, [
//                    'required' => false,
//                    'allow_delete' => true,
//                    'download_link' => false,
//                    'label' => 'Doc. Frente'
//                ])
//                ->add('ccBack', VichImageType::class, [
//                    'required' => false,
//                    'allow_delete' => true,
//                    'download_link' => false,
//                    'label' => 'Doc. Atrás'
//                ])
//                ->add('certBank', VichImageType::class, [
//                    'required' => false,
//                    'allow_delete' => true,
//                    'download_link' => false,
//                    'label' => 'Cert. Bancaria'
//                ])
                ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }
    
    protected function addElements(FormInterface $form, $country = null) {
        $cities = array();
        if ($country) {
            $cities = $this->em->getRepository('AppBundle:City')
                ->createQueryBuilder("q")
                ->where("q.country = :country")
                ->setParameter("country", $country)
                ->getQuery()
                ->getResult();
        }
        $form->add('city', EntityType::class, array(
            'label' => 'Ciudad',
            'required' => true,
            'choices' => $cities,
            'class' => City::class,
            'attr' => [
                'class' => 'select2'
            ]
        ));
    }
    
    function onPreSubmit(FormEvent $event) {
      $em = $this->em;
      $form = $event->getForm();
      $data = $event->getData();
      $country = $data['country'];
      $city = $data['city'];
      if($country && $city){
        $countryCheck = $em->getRepository('AppBundle:Country')->find($country);
        $cityCheck = $em->getRepository('AppBundle:City')->find($city);
        if(is_null($cityCheck) || !$cityCheck){
          $cityCheck = $em->getRepository('AppBundle:City')->findOneBy(['name'=>$city,'country'=>$countryCheck]);
          if(is_null($cityCheck) || !$cityCheck){
            $cityCheck = new Ciudad();
            $cityCheck->setCountry($countryCheck);
            $cityCheck->setName($city);
            $em->persist($cityCheck);
            $em->flush($cityCheck);
            $em->refresh($cityCheck);
          }
        }
        $this->addElements($form, $country);
        $data['city'] = $cityCheck->getId();
        $event->setData($data);
      }else{
        $this->addElements($form, $country);
      }
    }
    
    function onPreSetData(FormEvent $event) {
        $user = $event->getData();
        $form = $event->getForm();
        if($user){
          $country = $user->getCountry() ? $user->getCountry() : $this->countries[0];
        }else{
          $country = $this->countries[0];
        }
        $this->addElements($form, $country);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\FosUser',
            'roles' => array()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_fosuser';
    }


}
