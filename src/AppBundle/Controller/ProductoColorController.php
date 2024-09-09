<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProductoColor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Productocolor controller.
 *
 */
class ProductoColorController extends Controller
{
    /**
     * Lists all productoColor entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProductoColor'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $productoColorsQ = $em->getRepository('AppBundle:ProductoColor')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProductoColor', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $productoColorsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $productoColorsQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $productoColorsQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $productoColors = $pagination->getItems();
        

        return $this->render('productocolor/index.html.twig', array(
            'productoColors' => $productoColors,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new productoColor entity.
     *
     */
    public function newAction(Request $request)
    {
        $productoColor = new Productocolor();
        $form = $this->createForm('AppBundle\Form\ProductoColorType', $productoColor);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productoColor);
            $em->flush($productoColor);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productocolor_index');

        }

        return $this->render('productocolor/new.html.twig', array(
            'productoColor' => $productoColor,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a productoColor entity.
     *
     */
    public function showAction(ProductoColor $productoColor)
    {
        $deleteForm = $this->createDeleteForm($productoColor);

        return $this->render('productocolor/show.html.twig', array(
            'productoColor' => $productoColor,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing productoColor entity.
     *
     */
    public function editAction(Request $request, ProductoColor $productoColor)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($productoColor);
        $editForm = $this->createForm('AppBundle\Form\ProductoColorType', $productoColor);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('productocolor_index');
        }

        return $this->render('productocolor/edit.html.twig', array(
            'productoColor' => $productoColor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a productoColor entity.
     *
     */
    public function deleteAction(Request $request, ProductoColor $productoColor)
    {
        $form = $this->createDeleteForm($productoColor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productoColor);
            $em->flush($productoColor);
        }

        return $this->redirectToRoute('productocolor_index');
    }

    /**
     * Creates a form to delete a productoColor entity.
     *
     * @param ProductoColor $productoColor The productoColor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProductoColor $productoColor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productocolor_delete', array('id' => $productoColor->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProductoColor $productoColor)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($productoColor);
        $em->flush($productoColor);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('productocolor_index');
    }
}
