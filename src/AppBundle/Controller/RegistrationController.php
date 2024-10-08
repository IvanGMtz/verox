<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\ORM\Query;

/**
 * Controller managing the registration.
 *
 */
class RegistrationController extends BaseController
{
    private $eventDispatcher;
    private $formFactory;
    private $userManager;
    private $tokenStorage;
    private $em;
    private $letters;

    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager, TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        parent::__construct($eventDispatcher, $formFactory,$userManager, $tokenStorage);
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
//        $this->letters = ['A','B','C','D','E','F','G','H','I','J','K','L','M','P','Q','R','S','T','U','W','X','Y','Z','1','2','3','4','5','6','7','8','9'];
        $this->letters = ['0','1','2','3','4','5','6','7','8','9'];
    }
    
    public function generateRandomCode($size){
      $code = '';
      for($i = 0; $i < $size; $i++){
        $randomElement = $this->letters[array_rand($this->letters)];
        $code .= $randomElement;
      }
      return $code;
    }
    
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $em = $this->em;
        $now = new \DateTime();
        $user = $this->userManager->createUser();
        $user->setEnabled(true);
        
        $paises = $em->getRepository('AppBundle:Country')->createQueryBuilder('a')->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                
                $checkUsername = $em->getRepository('AppBundle:FosUser')
                        ->createQueryBuilder('a')
                        ->where('a.usernameCanonical = :username')
                        ->setParameter('username', strtolower($user->getUsername()))
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult()
                        ;
                if(!is_null($checkUsername)){
                  $this->addFlash('error', 'El nombre de usuario ya se encuentra registrado');
                  return $this->render('@FOSUser/Registration/register.html.twig', array(
                      'form' => $form->createView(),
                      'user' => $user,
                      'parentCode' => $parentCode
                  ));
                }
                
                $checkEmail = $em->getRepository('AppBundle:FosUser')
                        ->createQueryBuilder('a')
                        ->where('a.emailCanonical = :email')
                        ->setParameter('email', strtolower($user->getEmail()))
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult()
                        ;
                if(!is_null($checkEmail)){
                  $this->addFlash('error', 'El email ya se encuentra registrado');
                  return $this->render('@FOSUser/Registration/register.html.twig', array(
                      'form' => $form->createView(),
                      'user' => $user,
                      'parentCode' => $parentCode
                  ));
                }
                
                $checkDoc = $em->getRepository('AppBundle:FosUser')
                        ->createQueryBuilder('a')
                        ->where('a.doc = :doc')
                        ->setParameter('doc', strtolower($user->getDoc()))
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult()
                        ;
                if(!is_null($checkDoc)){
                  $this->addFlash('error', 'El documento ingresado ya se encuentra registrado');
                  return $this->render('@FOSUser/Registration/register.html.twig', array(
                      'form' => $form->createView(),
                      'user' => $user,
                      'parentCode' => $parentCode
                  ));
                }
                
                $event = new FormEvent($form, $request);
                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
                
                $randomCode = $this->generateRandomCode(12);
                $checkCode = $em->getRepository('AppBundle:FosUser')
                        ->createQueryBuilder('a')
                        ->where('a.serial = :serial')
                        ->setParameter('serial', $randomCode)
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult()
                        ;
                while(!is_null($checkCode)){
                  $randomCode = $this->generateRandomCode(12);
                  $checkCode = $em->getRepository('AppBundle:FosUser')
                          ->createQueryBuilder('a')
                          ->where('a.serial = :serial')
                          ->setParameter('serial', $randomCode)
                          ->setMaxResults(1)
                          ->getQuery()
                          ->getOneOrNullResult()
                          ;
                }
                $user->setSerial($randomCode);
                $user->setRegisteredAt($now);
                $this->userManager->updateUser($user);
                
                $userx = $em->getRepository('AppBundle:FosUser')->find($user->getId());
                $userx->addRole('ROLE_USER');
                $em->persist($userx);
                $em->flush();
                

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }

            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return $this->render('@FOSUser/Registration/register.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
            'paises' => json_encode($paises)
        ));
    }
    
    /**
     * Tell the user to check their email provider.
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->getSession()->get('fos_user_send_confirmation_email/email');

        if (empty($email)) {
            return new RedirectResponse($this->generateUrl('fos_user_registration_register'));
        }

        $request->getSession()->remove('fos_user_send_confirmation_email/email');
        $user = $this->userManager->findUserByEmail($email);

        if (null === $user) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login'));
        }

        return $this->render('@FOSUser/Registration/check_email.html.twig', array(
            'user' => $user,
        ));
    }
    
    /**
     * Receive the confirmation token from user email provider, login the user.
     *
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     */
    public function confirmAction(Request $request, $token)
    {
        $userManager = $this->userManager;

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }
        
        $user->addRole('ROLE_USER');
        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }
    
    /**
     * Tell the user his account is now confirmed.
     */
    public function confirmedAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('@FOSUser/Registration/confirmed.html.twig', array(
            'user' => $user,
            'targetUrl' => $this->generateUrl('avanzu_admin_home')
//            'targetUrl' => $this->getTargetUrlFromSession($request->getSession()),
        ));
    }

    /**
     * @return string|null
     */
    private function getTargetUrlFromSession(SessionInterface $session)
    {
        $key = sprintf('_security.%s.target_path', $this->tokenStorage->getToken()->getProviderKey());

        if ($session->has($key)) {
            return $session->get($key);
        }

        return null;
    }
}
