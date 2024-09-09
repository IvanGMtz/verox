<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProductoTalla;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Productotalla controller.
 *
 */
class ProductoTallaController extends Controller
{
    /**
     * Lists all productoTalla entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProductoTalla'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $productoTallasQ = $em->getRepository('AppBundle:ProductoTalla')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProductoTalla', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $productoTallasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $productoTallasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $productoTallasQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $productoTallas = $pagination->getItems();
        

        return $this->render('productotalla/index.html.twig', array(
            'productoTallas' => $productoTallas,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new productoTalla entity.
     *
     */
    public function newAction(Request $request)
    {
        $productoTalla = new Productotalla();
        $form = $this->createForm('AppBundle\Form\ProductoTallaType', $productoTalla);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productoTalla);
            $em->flush($productoTalla);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productotalla_index');

        }

        return $this->render('productotalla/new.html.twig', array(
            'productoTalla' => $productoTalla,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a productoTalla entity.
     *
     */
    public function showAction(ProductoTalla $productoTalla)
    {
        $deleteForm = $this->createDeleteForm($productoTalla);

        return $this->render('productotalla/show.html.twig', array(
            'productoTalla' => $productoTalla,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing productoTalla entity.
     *
     */
    public function editAction(Request $request, ProductoTalla $productoTalla)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($productoTalla);
        $editForm = $this->createForm('AppBundle\Form\ProductoTallaType', $productoTalla);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('productotalla_index');
        }

        return $this->render('productotalla/edit.html.twig', array(
            'productoTalla' => $productoTalla,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a productoTalla entity.
     *
     */
    public function deleteAction(Request $request, ProductoTalla $productoTalla)
    {
        $form = $this->createDeleteForm($productoTalla);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productoTalla);
            $em->flush($productoTalla);
        }

        return $this->redirectToRoute('productotalla_index');
    }

    /**
     * Creates a form to delete a productoTalla entity.
     *
     * @param ProductoTalla $productoTalla The productoTalla entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProductoTalla $productoTalla)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productotalla_delete', array('id' => $productoTalla->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProductoTalla $productoTalla)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($productoTalla);
        $em->flush($productoTalla);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('productotalla_index');
    }
}
