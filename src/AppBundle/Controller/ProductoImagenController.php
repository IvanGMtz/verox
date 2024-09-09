<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProductoImagen;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Productoimagen controller.
 *
 */
class ProductoImagenController extends Controller
{
    /**
     * Lists all productoImagen entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProductoImagen'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $productoImagensQ = $em->getRepository('AppBundle:ProductoImagen')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProductoImagen', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $productoImagensQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $productoImagensQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $productoImagensQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $productoImagens = $pagination->getItems();
        

        return $this->render('productoimagen/index.html.twig', array(
            'productoImagens' => $productoImagens,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new productoImagen entity.
     *
     */
    public function newAction(Request $request)
    {
        $productoImagen = new Productoimagen();
        $form = $this->createForm('AppBundle\Form\ProductoImagenType', $productoImagen);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productoImagen);
            $em->flush($productoImagen);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productoimagen_index');

        }

        return $this->render('productoimagen/new.html.twig', array(
            'productoImagen' => $productoImagen,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a productoImagen entity.
     *
     */
    public function showAction(ProductoImagen $productoImagen)
    {
        $deleteForm = $this->createDeleteForm($productoImagen);

        return $this->render('productoimagen/show.html.twig', array(
            'productoImagen' => $productoImagen,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing productoImagen entity.
     *
     */
    public function editAction(Request $request, ProductoImagen $productoImagen)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($productoImagen);
        $editForm = $this->createForm('AppBundle\Form\ProductoImagenType', $productoImagen);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('productoimagen_index');
        }

        return $this->render('productoimagen/edit.html.twig', array(
            'productoImagen' => $productoImagen,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a productoImagen entity.
     *
     */
    public function deleteAction(Request $request, ProductoImagen $productoImagen)
    {
        $form = $this->createDeleteForm($productoImagen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productoImagen);
            $em->flush($productoImagen);
        }

        return $this->redirectToRoute('productoimagen_index');
    }

    /**
     * Creates a form to delete a productoImagen entity.
     *
     * @param ProductoImagen $productoImagen The productoImagen entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProductoImagen $productoImagen)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productoimagen_delete', array('id' => $productoImagen->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProductoImagen $productoImagen)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($productoImagen);
        $em->flush($productoImagen);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('productoimagen_index');
    }
}
