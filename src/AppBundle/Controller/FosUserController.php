<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FosUser;
use AppBundle\Entity\Cycle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Fosuser controller.
 *
 */
class FosUserController extends Controller
{
    public function generateRandomCode($size){
      $letters = ['0','1','2','3','4','5','6','7','8','9'];
      $code = '';
      for($i = 0; $i < $size; $i++){
        $randomElement = $letters[array_rand($letters)];
        $code .= $randomElement;
      }
      return $code;
    }
    
    public function citiesAction(Request $request)
    {
        $country_id = $request->query->get('country');
        $em = $this->getDoctrine()->getManager();
        $cities = array();
        $citiesObj = $em->getRepository('AppBundle:City')->findBy(array('country'=>$country_id));
        foreach($citiesObj as $c){
          array_push($cities,array('id'=>$c->getId(),'name'=>$c->getName()));
        }
        return new JsonResponse($cities);
    }
    
    /**
     * Lists all fosUser entities.
     *
     */
    public function indexAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $this->denyAccessUnlessGranted(['ROLE_SUPER_ADMIN','ROLE_ADMIN_PRODUCCION'], null, 'Unable to access this page!');
        $em = $this->getDoctrine()->getManager();
        $q = $request->query->get('q',false);
        if($q && $q !=''){
        }
        $fosUsers = $em->getRepository('AppBundle:FosUser')
          ->createQueryBuilder('a')
          ->getQuery()
          ->getResult()
          ;
        return $this->render('fosuser/index.html.twig', array(
            'fosUsers' => $fosUsers,
        ));
    }

    /**
     * Creates a new fosUser entity.
     *
     */
    public function newAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        // if(!$user->hasRole('ROLE_SUPER_ADMIN') || !$user->hasRole('ROLE_ADMIN_PRODUCCION')){
        //   throw new AccessDeniedException('This user does not have access to this section.');
        // }
        
        $rols = $this->container->getParameter('security.role_hierarchy.roles');
        $roles = array();
        foreach($rols as $rol=>$value){
          $rolname = str_replace('ROLE_', '',$rol);
          if($rol != 'ROLE_USER'){
            $roles[$rolname] = $rol;
          }
        }
        
        $fosUser = new Fosuser();
        $form = $this->createForm('AppBundle\Form\FosUserType', $fosUser, array('roles'=>$roles));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          try {
            $em = $this->getDoctrine()->getManager();
            $fosUser->setEnabled(true);
            $em->persist($fosUser);
            $em->flush($fosUser);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('fosuser_index');
          } catch (\Throwable $th) {
            $this->addFlash(
                'error',
                'El Correo ya existe para otro usuario'
            );
            return $this->redirectToRoute('fosuser_index');
          }
            
        }
        return $this->render('fosuser/new.html.twig', array(
            'fosUser' => $fosUser,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a fosUser entity.
     *
     */
    public function showAction(FosUser $fosUser)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:FosUser')
        ->createQueryBuilder('a')
        ->where('a.id = :id')
        ->setParameter('id', $fosUser->getId())
        ->getQuery()
        ->getResult();
        ;
        //dump($user);exit;
        return $this->render('fosuser/show.html.twig', array(
            'fosUser' => $user[0]
        ));
    }

    /**
     * Displays a form to edit an existing fosUser entity.
     *
     */
    public function editAction(Request $request, FosUser $fosUser)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        // if(!$user->hasRole('ROLE_SUPER_ADMIN') || !$user->hasRole('ROLE_ADMIN_PRODUCCION')){
        //   $fosUser = $user;
        // }
        $lastpass = $fosUser->getPassword();
        $lastname = $fosUser->getName();
        $lastusername = $fosUser->getUsername();
        $lastdoctype = $fosUser->getDocType();
        $lastdoc = $fosUser->getDoc();
        $lastroles = $fosUser->getRoles();
        $rols = $this->container->getParameter('security.role_hierarchy.roles');
        $roles = array();
        foreach($rols as $rol=>$value){
          $rolname = str_replace('ROLE_', '',$rol);
          if($rol != 'ROLE_USER'){
            $roles[$rolname] = $rol;
          }
        }
        $editForm = $this->createForm('AppBundle\Form\FosUserType', $fosUser, array('roles'=>$roles));
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $editForm->getData();
            $emailOld = $em->getRepository('AppBundle:FosUser')
                    ->createQueryBuilder('a')
                    ->where('a.email LIKE :email')
                    ->andWhere('a.id <> :id')
                    ->setParameter('email',$fosUser->getEmail())
                    ->setParameter('id',$fosUser->getId())
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
            if(!is_null($emailOld)){
              $this->addFlash('error', 'El email ingresado ya está registrado');
              return $this->render('fosuser/edit.html.twig', array(
                  'fosUser' => $fosUser,
                  'edit_form' => $editForm->createView(),
              ));
            }
            
            $emailCanonicalOld = $em->getRepository('AppBundle:FosUser')
                    ->createQueryBuilder('a')
                    ->where('a.emailCanonical = :emailCanonical')
                    ->andWhere('a.id <> :id')
                    ->setParameter('emailCanonical',strtolower($fosUser->getEmail()))
                    ->setParameter('id',$fosUser->getId())
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
            if(!is_null($emailCanonicalOld)){
              $this->addFlash('error', 'El email ingresado ya está siendo utilizado');
              return $this->render('fosuser/edit.html.twig', array(
                  'fosUser' => $fosUser,
                  'edit_form' => $editForm->createView(),
              ));
            }
            
            $usernameOld = $em->getRepository('AppBundle:FosUser')
                    ->createQueryBuilder('a')
                    ->where('a.usernameCanonical = :username')
                    ->andWhere('a.id <> :id')
                    ->setParameter('username', strtolower($data->getUsername()))
                    ->setParameter('id',$fosUser->getId())
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
            if(!is_null($usernameOld)){
              $this->addFlash('error', 'El nombre de usuario ya está siendo utilizado');
              return $this->render('fosuser/edit.html.twig', array(
                  'fosUser' => $fosUser,
                  'edit_form' => $editForm->createView(),
              ));
            }
            
            $docOld = $em->getRepository('AppBundle:FosUser')
                    ->createQueryBuilder('a')
                    ->where('a.doc LIKE :doc')
                    ->andWhere('a.id <> :id')
                    ->setParameter('doc',$fosUser->getDoc())
                    ->setParameter('id',$fosUser->getId())
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
            if(!is_null($docOld)){
              $this->addFlash('error', 'El documento ya está registrado');
              return $this->render('fosuser/edit.html.twig', array(
                  'fosUser' => $fosUser,
                  'edit_form' => $editForm->createView(),
              ));
            }
           if(!$data->getPlainPassword()){
             $fosUser->setPassword($lastpass);
           }else{
             $fosUser->setPassword($data->getPlainPassword());
             // $fosUser->setPlainPassword($data->getPlainPassword());
           }
            $fosUser->setRoles($lastroles);
            $em->persist($fosUser);
            $em->flush();
            //dump($fosUser->getPassword(), $fosUser->getUserName()); exit;
            $this->addFlash(
                'success',
                'Cuenta actualizada correctamente'
            );
            // return $this->redirectToRoute('fosuser_edit', ['id'=>$fosUser->getId()]);
            return $this->redirectToRoute('fosuser_index');
        }

        return $this->render('fosuser/edit.html.twig', array(
            'fosUser' => $fosUser,
            'edit_form' => $editForm->createView(),
        ));
    }
    
    public function changeStateAction(FosUser $fosUser)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        // if(!$user->hasRole('ROLE_SUPER_ADMIN') || !$user->hasRole('ROLE_ADMIN_PRODUCCION')){
        //   throw new AccessDeniedException('This user does not have access to this section.');
        // }
        $fosUser->setEnabled(!$fosUser->isEnabled());
        $em->persist($fosUser);
        $em->flush();
        if($fosUser->isEnabled()){
          $this->addFlash('success', 'Usuario habilitado correctamente');
        }else{
          $this->addFlash('success', 'Usuario deshabilitado correctamente');
        }
        return $this->redirectToRoute('fosuser_index');
    }
    
    public function testEmailAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if(!$user->hasRole('ROLE_SUPER_ADMIN') || !$user->hasRole('ROLE_ADMIN_PRODUCCION')){
          throw new AccessDeniedException('This user does not have access to this section.');
        }
        $message = (new \Swift_Message('Email de prueba'))
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($request->query->get('email'))
            ->setBody(
                'Prueba exitosa'
            )
        ;
        $this->get('mailer')->send($message, $errors);
        return $this->redirectToRoute('fosuser_index');
    }
    
}
