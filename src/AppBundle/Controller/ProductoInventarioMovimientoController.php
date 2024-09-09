<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProductoInventarioMovimiento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Productoinventariomovimiento controller.
 *
 */
class ProductoInventarioMovimientoController extends Controller
{
    /**
     * Lists all productoInventarioMovimiento entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProductoInventarioMovimiento'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $productoInventarioMovimientosQ = $em->getRepository('AppBundle:ProductoInventarioMovimiento')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProductoInventarioMovimiento', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $productoInventarioMovimientosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $productoInventarioMovimientosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $productoInventarioMovimientosQ->orderBy('a.id', 'DESC')
        ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $productoInventarioMovimientos = $pagination->getItems();
        

        return $this->render('productoinventariomovimiento/index.html.twig', array(
            'productoInventarioMovimientos' => $productoInventarioMovimientos,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new productoInventarioMovimiento entity.
     *
     */
    public function newAction(Request $request)
    {
        $productoInventarioMovimiento = new Productoinventariomovimiento();
        $form = $this->createForm('AppBundle\Form\ProductoInventarioMovimientoType', $productoInventarioMovimiento);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productoInventarioMovimiento);
            $em->flush($productoInventarioMovimiento);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productoinventariomovimiento_index');

        }

        return $this->render('productoinventariomovimiento/new.html.twig', array(
            'productoInventarioMovimiento' => $productoInventarioMovimiento,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a productoInventarioMovimiento entity.
     *
     */
    public function showAction(ProductoInventarioMovimiento $productoInventarioMovimiento)
    {
        $deleteForm = $this->createDeleteForm($productoInventarioMovimiento);

        return $this->render('productoinventariomovimiento/show.html.twig', array(
            'productoInventarioMovimiento' => $productoInventarioMovimiento,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing productoInventarioMovimiento entity.
     *
     */
    public function editAction(Request $request, ProductoInventarioMovimiento $productoInventarioMovimiento)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($productoInventarioMovimiento);
        $editForm = $this->createForm('AppBundle\Form\ProductoInventarioMovimientoType', $productoInventarioMovimiento);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('productoinventariomovimiento_index');
        }

        return $this->render('productoinventariomovimiento/edit.html.twig', array(
            'productoInventarioMovimiento' => $productoInventarioMovimiento,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a productoInventarioMovimiento entity.
     *
     */
    public function deleteAction(Request $request, ProductoInventarioMovimiento $productoInventarioMovimiento)
    {
        $form = $this->createDeleteForm($productoInventarioMovimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productoInventarioMovimiento);
            $em->flush($productoInventarioMovimiento);
        }

        return $this->redirectToRoute('productoinventariomovimiento_index');
    }

    /**
     * Creates a form to delete a productoInventarioMovimiento entity.
     *
     * @param ProductoInventarioMovimiento $productoInventarioMovimiento The productoInventarioMovimiento entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProductoInventarioMovimiento $productoInventarioMovimiento)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productoinventariomovimiento_delete', array('id' => $productoInventarioMovimiento->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProductoInventarioMovimiento $productoInventarioMovimiento)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($productoInventarioMovimiento);
        $em->flush($productoInventarioMovimiento);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('productoinventariomovimiento_index');
    }
}
