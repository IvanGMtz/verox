<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DespachoOrdenItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Despachoordenitem controller.
 *
 */
class DespachoOrdenItemController extends Controller
{
    /**
     * Lists all despachoOrdenItem entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.DespachoOrdenItem'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $despachoOrdenItemsQ = $em
        ->getRepository('AppBundle:DespachoOrdenItem')
        ->createQueryBuilder('a')
        ->join('a.producto','p')
        ->join('p.producto','p2');
        
        if($q && $q !=''){
          $this->get('session')->set('q.DespachoOrdenItem', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
                if($qcount == 0){
                    if($field=="referencia"){
                        $despachoOrdenItemsQ->where('p2.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                    }
                    else if($field=="talla"){
                        $despachoOrdenItemsQ->where('p.nombre LIKE :nombre')->setParameter('nombre', '%'.$value.'%');
                    }
                    else if($field=="estatus"){
                        if(strtolower($value)=="despachado"){$value=2;}
                        if(strtolower($value)=="por despachar"){$value=1;}
                        $despachoOrdenItemsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                    }
                    else{
                        $despachoOrdenItemsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                    }
                    
                }else{
                    if($field=="referencia"){
                        $despachoOrdenItemsQ->andwhere('p2.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                    }
                    else if($field=="talla"){
                        $despachoOrdenItemsQ->andwhere('p.nombre LIKE :nombre')->setParameter('nombre', '%'.$value.'%');
                    }
                    else if($field=="estatus"){
                        if(strtolower($value)=="despachado"){$value=2;}
                        if(strtolower($value)=="por despachar"){$value=1;}
                        $despachoOrdenItemsQ->andwhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                    }
                    else{
                        $despachoOrdenItemsQ->andwhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                    }
                }
                $qcount++;
            }
          }
        }
        
        $query = $despachoOrdenItemsQ->orderBy('a.ordenDespacho', 'DESC')
        ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $despachoOrdenItems = $pagination->getItems();
        

        return $this->render('despachoordenitem/index.html.twig', array(
            'despachoOrdenItems' => $despachoOrdenItems,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new despachoOrdenItem entity.
     *
     */
    public function newAction(Request $request)
    {
        $despachoOrdenItem = new Despachoordenitem();
        $form = $this->createForm('AppBundle\Form\DespachoOrdenItemType', $despachoOrdenItem);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($despachoOrdenItem);
            $em->flush($despachoOrdenItem);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('despachoordenitem_index');

        }

        return $this->render('despachoordenitem/new.html.twig', array(
            'despachoOrdenItem' => $despachoOrdenItem,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a despachoOrdenItem entity.
     *
     */
    public function showAction(DespachoOrdenItem $despachoOrdenItem)
    {
        $deleteForm = $this->createDeleteForm($despachoOrdenItem);

        return $this->render('despachoordenitem/show.html.twig', array(
            'despachoOrdenItem' => $despachoOrdenItem,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing despachoOrdenItem entity.
     *
     */
    public function editAction(Request $request, DespachoOrdenItem $despachoOrdenItem)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($despachoOrdenItem);
        $editForm = $this->createForm('AppBundle\Form\DespachoOrdenItemType', $despachoOrdenItem);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('despachoordenitem_index');
        }

        return $this->render('despachoordenitem/edit.html.twig', array(
            'despachoOrdenItem' => $despachoOrdenItem,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a despachoOrdenItem entity.
     *
     */
    public function deleteAction(Request $request, DespachoOrdenItem $despachoOrdenItem)
    {
        try {
            $form = $this->createDeleteForm($despachoOrdenItem);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($despachoOrdenItem);
                $em->flush($despachoOrdenItem);
            }

            return $this->redirectToRoute('despachoordenitem_index');
        } catch (\Throwable $th) {
            dump($th);exit;
        }
    }

    /**
     * Creates a form to delete a despachoOrdenItem entity.
     *
     * @param DespachoOrdenItem $despachoOrdenItem The despachoOrdenItem entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DespachoOrdenItem $despachoOrdenItem)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('despachoordenitem_delete', array('id' => $despachoOrdenItem->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(DespachoOrdenItem $despachoOrdenItem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($despachoOrdenItem);
        $em->flush($despachoOrdenItem);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('despachoordenitem_index');
    }
}
