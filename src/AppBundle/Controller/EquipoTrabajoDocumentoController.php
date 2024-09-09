<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EquipoTrabajoDocumento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Equipotrabajodocumento controller.
 *
 */
class EquipoTrabajoDocumentoController extends Controller
{
    /**
     * Lists all equipoTrabajoDocumento entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.EquipoTrabajoDocumento'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $equipoTrabajoDocumentosQ = $em->getRepository('AppBundle:EquipoTrabajoDocumento')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.EquipoTrabajoDocumento', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $equipoTrabajoDocumentosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $equipoTrabajoDocumentosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $equipoTrabajoDocumentosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $equipoTrabajoDocumentos = $pagination->getItems();
        

        return $this->render('equipotrabajodocumento/index.html.twig', array(
            'equipoTrabajoDocumentos' => $equipoTrabajoDocumentos,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new equipoTrabajoDocumento entity.
     *
     */
    public function newAction(Request $request)
    {
        $equipoTrabajoDocumento = new Equipotrabajodocumento();
        $form = $this->createForm('AppBundle\Form\EquipoTrabajoDocumentoType', $equipoTrabajoDocumento);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($equipoTrabajoDocumento);
            $em->flush($equipoTrabajoDocumento);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('equipotrabajodocumento_index');

        }

        return $this->render('equipotrabajodocumento/new.html.twig', array(
            'equipoTrabajoDocumento' => $equipoTrabajoDocumento,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a equipoTrabajoDocumento entity.
     *
     */
    public function showAction(EquipoTrabajoDocumento $equipoTrabajoDocumento)
    {
        $deleteForm = $this->createDeleteForm($equipoTrabajoDocumento);

        return $this->render('equipotrabajodocumento/show.html.twig', array(
            'equipoTrabajoDocumento' => $equipoTrabajoDocumento,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing equipoTrabajoDocumento entity.
     *
     */
    public function editAction(Request $request, EquipoTrabajoDocumento $equipoTrabajoDocumento)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($equipoTrabajoDocumento);
        $editForm = $this->createForm('AppBundle\Form\EquipoTrabajoDocumentoType', $equipoTrabajoDocumento);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('equipotrabajodocumento_index');
        }

        return $this->render('equipotrabajodocumento/edit.html.twig', array(
            'equipoTrabajoDocumento' => $equipoTrabajoDocumento,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a equipoTrabajoDocumento entity.
     *
     */
    public function deleteAction(Request $request, EquipoTrabajoDocumento $equipoTrabajoDocumento)
    {
        $form = $this->createDeleteForm($equipoTrabajoDocumento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($equipoTrabajoDocumento);
            $em->flush($equipoTrabajoDocumento);
        }

        return $this->redirectToRoute('equipotrabajodocumento_index');
    }

    /**
     * Creates a form to delete a equipoTrabajoDocumento entity.
     *
     * @param EquipoTrabajoDocumento $equipoTrabajoDocumento The equipoTrabajoDocumento entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EquipoTrabajoDocumento $equipoTrabajoDocumento)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('equipotrabajodocumento_delete', array('id' => $equipoTrabajoDocumento->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(EquipoTrabajoDocumento $equipoTrabajoDocumento)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($equipoTrabajoDocumento);
        $em->flush($equipoTrabajoDocumento);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('equipotrabajodocumento_index');
    }
}
