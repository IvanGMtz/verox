<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProductoCategoria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Productocategorium controller.
 *
 */
class ProductoCategoriaController extends Controller
{
    /**
     * Lists all productoCategorium entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProductoCategoria'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $productoCategoriasQ = $em->getRepository('AppBundle:ProductoCategoria')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProductoCategoria', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $productoCategoriasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $productoCategoriasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $productoCategoriasQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $productoCategorias = $pagination->getItems();
        

        return $this->render('productocategoria/index.html.twig', array(
            'productoCategorias' => $productoCategorias,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new productoCategorium entity.
     *
     */
    public function newAction(Request $request)
    {
        $productoCategoria = new Productocategoria();
        $form = $this->createForm('AppBundle\Form\ProductoCategoriaType', $productoCategoria);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productoCategoria);
            $em->flush($productoCategoria);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productocategoria_index');

        }

        return $this->render('productocategoria/new.html.twig', array(
            'productoCategorium' => $productoCategoria,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a productoCategorium entity.
     *
     */
    public function showAction(ProductoCategoria $productoCategoria)
    {
        $deleteForm = $this->createDeleteForm($productoCategoria);

        return $this->render('productocategoria/show.html.twig', array(
            'productoCategorium' => $productoCategoria,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing productoCategorium entity.
     *
     */
    public function editAction(Request $request, ProductoCategoria $productoCategoria)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($productoCategoria);
        $editForm = $this->createForm('AppBundle\Form\ProductoCategoriaType', $productoCategoria);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('productocategoria_index');
        }

        return $this->render('productocategoria/edit.html.twig', array(
            'productoCategorium' => $productoCategoria,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a productoCategorium entity.
     *
     */
    public function deleteAction(Request $request, ProductoCategoria $productoCategoria)
    {
        $form = $this->createDeleteForm($productoCategoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productoCategoria);
            $em->flush($productoCategoria);
        }

        return $this->redirectToRoute('productocategoria_index');
    }

    /**
     * Creates a form to delete a productoCategorium entity.
     *
     * @param ProductoCategoria $productoCategoria The productoCategorium entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProductoCategoria $productoCategoria)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productocategoria_delete', array('id' => $productoCategoria->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProductoCategoria $productoCategoria)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($productoCategoria);
        $em->flush($productoCategoria);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('productocategoria_index');
    }
}
