<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Inventario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Inventario controller.
 *
 */
class InventarioController extends Controller
{
    /**
     * Lists all inventario entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.Inventario'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $inventariosQ = $em->getRepository('AppBundle:Inventario')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.Inventario', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $inventariosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $inventariosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $inventariosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            5000 /*limit per page*/
        );
        
        $inventarios = $pagination->getItems();
        
        $almacenes = $em->getRepository('AppBundle:Almacen')->findAll();

        return $this->render('inventario/index.html.twig', array(
            'inventarios' => $inventarios,
            'q' => $q,
            'pagination' => $pagination,
            'almacenes' => $almacenes
        ));
    }

    /**
     * Creates a new inventario entity.
     *
     */
    public function newAction(Request $request)
    {
        $inventario = new Inventario();
        $form = $this->createForm('AppBundle\Form\InventarioType', $inventario);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($inventario);
            $em->flush($inventario);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('inventario_index');

        }

        return $this->render('inventario/new.html.twig', array(
            'inventario' => $inventario,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a inventario entity.
     *
     */
    public function showAction(Inventario $inventario)
    {
        $deleteForm = $this->createDeleteForm($inventario);

        return $this->render('inventario/show.html.twig', array(
            'inventario' => $inventario,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing inventario entity.
     *
     */
    public function editAction(Request $request, Inventario $inventario)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($inventario);
        $editForm = $this->createForm('AppBundle\Form\InventarioType', $inventario);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('inventario_index');
        }

        return $this->render('inventario/edit.html.twig', array(
            'inventario' => $inventario,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a inventario entity.
     *
     */
    public function deleteAction(Request $request, Inventario $inventario)
    {
        $form = $this->createDeleteForm($inventario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventario);
            $em->flush($inventario);
        }

        return $this->redirectToRoute('inventario_index');
    }

    /**
     * Creates a form to delete a inventario entity.
     *
     * @param Inventario $inventario The inventario entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Inventario $inventario)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventario_delete', array('id' => $inventario->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(Inventario $inventario)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($inventario);
        $em->flush($inventario);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('inventario_index');
    }
}
