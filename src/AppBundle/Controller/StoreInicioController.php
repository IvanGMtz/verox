<?php

namespace AppBundle\Controller;

use AppBundle\Entity\StoreInicio;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Storeinicio controller.
 *
 */
class StoreInicioController extends Controller
{
    /**
     * Lists all storeInicio entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.StoreInicio'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $storeIniciosQ = $em->getRepository('AppBundle:StoreInicio')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.StoreInicio', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $storeIniciosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $storeIniciosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $storeIniciosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $storeInicios = $pagination->getItems();
        

        return $this->render('storeinicio/index.html.twig', array(
            'storeInicios' => $storeInicios,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new storeInicio entity.
     *
     */
    public function newAction(Request $request)
    {
        $storeInicio = new Storeinicio();
        $form = $this->createForm('AppBundle\Form\StoreInicioType', $storeInicio);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($storeInicio);
            $em->flush($storeInicio);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('storeinicio_index');

        }

        return $this->render('storeinicio/new.html.twig', array(
            'storeInicio' => $storeInicio,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a storeInicio entity.
     *
     */
    public function showAction(StoreInicio $storeInicio)
    {
        $deleteForm = $this->createDeleteForm($storeInicio);

        return $this->render('storeinicio/show.html.twig', array(
            'storeInicio' => $storeInicio,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing storeInicio entity.
     *
     */
    public function editAction(Request $request, StoreInicio $storeInicio)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($storeInicio);
        $editForm = $this->createForm('AppBundle\Form\StoreInicioType', $storeInicio);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('storeinicio_index');
        }

        return $this->render('storeinicio/edit.html.twig', array(
            'storeInicio' => $storeInicio,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a storeInicio entity.
     *
     */
    public function deleteAction(Request $request, StoreInicio $storeInicio)
    {
        $form = $this->createDeleteForm($storeInicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($storeInicio);
            $em->flush($storeInicio);
        }

        return $this->redirectToRoute('storeinicio_index');
    }

    /**
     * Creates a form to delete a storeInicio entity.
     *
     * @param StoreInicio $storeInicio The storeInicio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StoreInicio $storeInicio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('storeinicio_delete', array('id' => $storeInicio->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(StoreInicio $storeInicio)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($storeInicio);
        $em->flush($storeInicio);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('storeinicio_index');
    }
}
