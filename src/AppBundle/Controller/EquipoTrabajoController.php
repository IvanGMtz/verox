<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EquipoTrabajo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Equipotrabajo controller.
 *
 */
class EquipoTrabajoController extends Controller
{
    /**
     * Lists all equipoTrabajo entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.EquipoTrabajo'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $equipoTrabajosQ = $em->getRepository('AppBundle:EquipoTrabajo')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.EquipoTrabajo', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $equipoTrabajosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $equipoTrabajosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $equipoTrabajosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $equipoTrabajos = $pagination->getItems();
        

        return $this->render('equipotrabajo/index.html.twig', array(
            'equipoTrabajos' => $equipoTrabajos,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new equipoTrabajo entity.
     *
     */
    public function newAction(Request $request)
    {
        $equipoTrabajo = new Equipotrabajo();
        $form = $this->createForm('AppBundle\Form\EquipoTrabajoType', $equipoTrabajo);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($equipoTrabajo);
            $em->flush($equipoTrabajo);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('equipotrabajo_index');

        }

        return $this->render('equipotrabajo/new.html.twig', array(
            'equipoTrabajo' => $equipoTrabajo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a equipoTrabajo entity.
     *
     */
    public function showAction(EquipoTrabajo $equipoTrabajo)
    {
        $deleteForm = $this->createDeleteForm($equipoTrabajo);

        return $this->render('equipotrabajo/show.html.twig', array(
            'equipoTrabajo' => $equipoTrabajo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing equipoTrabajo entity.
     *
     */
    public function editAction(Request $request, EquipoTrabajo $equipoTrabajo)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($equipoTrabajo);
        $editForm = $this->createForm('AppBundle\Form\EquipoTrabajoType', $equipoTrabajo);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('equipotrabajo_index');
        }

        return $this->render('equipotrabajo/edit.html.twig', array(
            'equipoTrabajo' => $equipoTrabajo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a equipoTrabajo entity.
     *
     */
    public function deleteAction(Request $request, EquipoTrabajo $equipoTrabajo)
    {
        $form = $this->createDeleteForm($equipoTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($equipoTrabajo);
            $em->flush($equipoTrabajo);
        }

        return $this->redirectToRoute('equipotrabajo_index');
    }

    /**
     * Creates a form to delete a equipoTrabajo entity.
     *
     * @param EquipoTrabajo $equipoTrabajo The equipoTrabajo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EquipoTrabajo $equipoTrabajo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('equipotrabajo_delete', array('id' => $equipoTrabajo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(EquipoTrabajo $equipoTrabajo)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($equipoTrabajo);
        $em->flush($equipoTrabajo);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('equipotrabajo_index');
    }
}
