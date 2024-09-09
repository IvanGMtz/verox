<?php

namespace AppBundle\Controller;

use AppBundle\Entity\InventarioOrdenItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Inventarioordenitem controller.
 *
 */
class InventarioOrdenItemController extends Controller
{
    /**
     * Lists all inventarioOrdenItem entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.InventarioOrdenItem'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $inventarioOrdenItemsQ = $em->getRepository('AppBundle:InventarioOrdenItem')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.InventarioOrdenItem', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $inventarioOrdenItemsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $inventarioOrdenItemsQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $inventarioOrdenItemsQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $inventarioOrdenItems = $pagination->getItems();
        

        return $this->render('inventarioordenitem/index.html.twig', array(
            'inventarioOrdenItems' => $inventarioOrdenItems,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new inventarioOrdenItem entity.
     *
     */
    public function newAction(Request $request)
    {
        $inventarioOrdenItem = new Inventarioordenitem();
        $form = $this->createForm('AppBundle\Form\InventarioOrdenItemType', $inventarioOrdenItem);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($inventarioOrdenItem);
            $em->flush($inventarioOrdenItem);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('inventarioordenitem_index');

        }

        return $this->render('inventarioordenitem/new.html.twig', array(
            'inventarioOrdenItem' => $inventarioOrdenItem,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a inventarioOrdenItem entity.
     *
     */
    public function showAction(InventarioOrdenItem $inventarioOrdenItem)
    {
        $deleteForm = $this->createDeleteForm($inventarioOrdenItem);

        return $this->render('inventarioordenitem/show.html.twig', array(
            'inventarioOrdenItem' => $inventarioOrdenItem,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing inventarioOrdenItem entity.
     *
     */
    public function editAction(Request $request, InventarioOrdenItem $inventarioOrdenItem)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($inventarioOrdenItem);
        $editForm = $this->createForm('AppBundle\Form\InventarioOrdenItemType', $inventarioOrdenItem);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('inventarioordenitem_index');
        }

        return $this->render('inventarioordenitem/edit.html.twig', array(
            'inventarioOrdenItem' => $inventarioOrdenItem,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a inventarioOrdenItem entity.
     *
     */
    public function deleteAction(Request $request, InventarioOrdenItem $inventarioOrdenItem)
    {
        $form = $this->createDeleteForm($inventarioOrdenItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventarioOrdenItem);
            $em->flush($inventarioOrdenItem);
        }

        return $this->redirectToRoute('inventarioordenitem_index');
    }

    /**
     * Creates a form to delete a inventarioOrdenItem entity.
     *
     * @param InventarioOrdenItem $inventarioOrdenItem The inventarioOrdenItem entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(InventarioOrdenItem $inventarioOrdenItem)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventarioordenitem_delete', array('id' => $inventarioOrdenItem->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(InventarioOrdenItem $inventarioOrdenItem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($inventarioOrdenItem);
        $em->flush($inventarioOrdenItem);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('inventarioordenitem_index');
    }
}
