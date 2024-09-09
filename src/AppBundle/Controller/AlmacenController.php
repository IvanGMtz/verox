<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Almacen;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Almacen controller.
 *
 */
class AlmacenController extends Controller
{
    /**
     * Lists all almacen entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.Almacen'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $almacensQ = $em->getRepository('AppBundle:Almacen')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.Almacen', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $almacensQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $almacensQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $almacensQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $almacens = $pagination->getItems();
        

        return $this->render('almacen/index.html.twig', array(
            'almacens' => $almacens,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new almacen entity.
     *
     */
    public function newAction(Request $request)
    {
        $almacen = new Almacen();
        $form = $this->createForm('AppBundle\Form\AlmacenType', $almacen);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $almacen->setFechaCreacion($fecha);
            $almacen->setUsuarioCreacion($user);
            $almacen->setEstado(1);
            $em->persist($almacen);
            $em->flush($almacen);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('almacen_index');

        }

        return $this->render('almacen/new.html.twig', array(
            'almacen' => $almacen,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a almacen entity.
     *
     */
    public function showAction(Almacen $almacen)
    {
        $deleteForm = $this->createDeleteForm($almacen);

        return $this->render('almacen/show.html.twig', array(
            'almacen' => $almacen,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing almacen entity.
     *
     */
    public function editAction(Request $request, Almacen $almacen)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($almacen);
        $editForm = $this->createForm('AppBundle\Form\AlmacenType', $almacen);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('almacen_index');
        }

        return $this->render('almacen/edit.html.twig', array(
            'almacen' => $almacen,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a almacen entity.
     *
     */
    public function deleteAction(Request $request, Almacen $almacen)
    {
        $form = $this->createDeleteForm($almacen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($almacen);
            $em->flush($almacen);
        }

        return $this->redirectToRoute('almacen_index');
    }

    /**
     * Creates a form to delete a almacen entity.
     *
     * @param Almacen $almacen The almacen entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Almacen $almacen)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('almacen_delete', array('id' => $almacen->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(Almacen $almacen)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($almacen);
        $em->flush($almacen);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('almacen_index');
    }
}
