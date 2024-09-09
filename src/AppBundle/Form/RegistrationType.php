<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Entity\Country;
use AppBundle\Entity\City;

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegistrationType extends AbstractType
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
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', TextType::class, array(
                    'label' => 'Nombres y Apellidos',
                    'required' => true
                ))
                ->add('email', EmailType::class, array(
                    'label' => 'Correo',
                    'required' => true
                ))
                ->add('username', TextType::class, array(
                    'label' => 'Usuario (ejemplo: pepe12)',
                    'required' => true
                ))
                ->add('phone', TextType::class, array(
                    'label' => 'Contacto WhatsApp',
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
//                ->add('birthday', BirthdayType::class, array(
//                    'label' => 'Fecha de nacimiento',
//                    'required' => false,
//                    'widget' => 'single_text',
//                ))
                ->add('country', EntityType::class, array(
                    'class' => Country::class,
                    'label' => 'País',
                    'required' => true,
                    'choices' => $this->countries,
                    'attr' => [
                        'class' => 'select2'
                    ]
                ))
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

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
