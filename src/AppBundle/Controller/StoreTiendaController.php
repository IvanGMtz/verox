<?php

namespace AppBundle\Controller;

use AppBundle\Entity\StoreTienda;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Storetienda controller.
 *
 */
class StoreTiendaController extends Controller
{
    /**
     * Lists all storeTienda entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.StoreTienda'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $storeTiendasQ = $em->getRepository('AppBundle:StoreTienda')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.StoreTienda', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $storeTiendasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $storeTiendasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $storeTiendasQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $storeTiendas = $pagination->getItems();
        

        return $this->render('storetienda/index.html.twig', array(
            'storeTiendas' => $storeTiendas,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new storeTienda entity.
     *
     */
    public function newAction(Request $request)
    {
        $storeTienda = new Storetienda();
        $form = $this->createForm('AppBundle\Form\StoreTiendaType', $storeTienda);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($storeTienda);
            $em->flush($storeTienda);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('storetienda_index');

        }

        return $this->render('storetienda/new.html.twig', array(
            'storeTienda' => $storeTienda,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a storeTienda entity.
     *
     */
    public function showAction(StoreTienda $storeTienda)
    {
        $deleteForm = $this->createDeleteForm($storeTienda);

        return $this->render('storetienda/show.html.twig', array(
            'storeTienda' => $storeTienda,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing storeTienda entity.
     *
     */
    public function editAction(Request $request, StoreTienda $storeTienda)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($storeTienda);
        $editForm = $this->createForm('AppBundle\Form\StoreTiendaType', $storeTienda);
        $editForm->handleRequest($request);
        //dump($request);exit;
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('storetienda_index');
        }
        return $this->render('storetienda/edit.html.twig', array(
            'storeTienda' => $storeTienda,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a storeTienda entity.
     *
     */
    public function deleteAction(Request $request, StoreTienda $storeTienda)
    {
        $form = $this->createDeleteForm($storeTienda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($storeTienda);
            $em->flush($storeTienda);
        }

        return $this->redirectToRoute('storetienda_index');
    }

    /**
     * Creates a form to delete a storeTienda entity.
     *
     * @param StoreTienda $storeTienda The storeTienda entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StoreTienda $storeTienda)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('storetienda_delete', array('id' => $storeTienda->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(StoreTienda $storeTienda)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($storeTienda);
        $em->flush($storeTienda);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('storetienda_index');
    }
}
