<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProductoDescripcion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Productodescripcion controller.
 *
 */
class ProductoDescripcionController extends Controller
{
    /**
     * Lists all productoDescripcion entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProductoDescripcion'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $productoDescripcionsQ = $em->getRepository('AppBundle:ProductoDescripcion')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProductoDescripcion', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $productoDescripcionsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $productoDescripcionsQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $productoDescripcionsQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $productoDescripcions = $pagination->getItems();
        

        return $this->render('productodescripcion/index.html.twig', array(
            'productoDescripcions' => $productoDescripcions,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new productoDescripcion entity.
     *
     */
    public function newAction(Request $request)
    {
        $productoDescripcion = new Productodescripcion();
        $form = $this->createForm('AppBundle\Form\ProductoDescripcionType', $productoDescripcion);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productoDescripcion);
            $em->flush($productoDescripcion);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productodescripcion_index');

        }

        return $this->render('productodescripcion/new.html.twig', array(
            'productoDescripcion' => $productoDescripcion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a productoDescripcion entity.
     *
     */
    public function showAction(ProductoDescripcion $productoDescripcion)
    {
        $deleteForm = $this->createDeleteForm($productoDescripcion);

        return $this->render('productodescripcion/show.html.twig', array(
            'productoDescripcion' => $productoDescripcion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing productoDescripcion entity.
     *
     */
    public function editAction(Request $request, ProductoDescripcion $productoDescripcion)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($productoDescripcion);
        $editForm = $this->createForm('AppBundle\Form\ProductoDescripcionType', $productoDescripcion);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('productodescripcion_index');
        }

        return $this->render('productodescripcion/edit.html.twig', array(
            'productoDescripcion' => $productoDescripcion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a productoDescripcion entity.
     *
     */
    public function deleteAction(Request $request, ProductoDescripcion $productoDescripcion)
    {
        $form = $this->createDeleteForm($productoDescripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productoDescripcion);
            $em->flush($productoDescripcion);
        }

        return $this->redirectToRoute('productodescripcion_index');
    }

    /**
     * Creates a form to delete a productoDescripcion entity.
     *
     * @param ProductoDescripcion $productoDescripcion The productoDescripcion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProductoDescripcion $productoDescripcion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productodescripcion_delete', array('id' => $productoDescripcion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProductoDescripcion $productoDescripcion)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($productoDescripcion);
        $em->flush($productoDescripcion);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('productodescripcion_index');
    }
}
