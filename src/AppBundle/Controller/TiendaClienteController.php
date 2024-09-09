<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TiendaCliente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Tiendacliente controller.
 *
 */
class TiendaClienteController extends Controller
{
    /**
     * Lists all tiendaCliente entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.TiendaCliente'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $tiendaClientesQ = $em->getRepository('AppBundle:TiendaCliente')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.TiendaCliente', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $tiendaClientesQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $tiendaClientesQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $tiendaClientesQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $tiendaClientes = $pagination->getItems();
        

        return $this->render('tiendacliente/index.html.twig', array(
            'tiendaClientes' => $tiendaClientes,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new tiendaCliente entity.
     *
     */
    public function newAction(Request $request)
    {
        $tiendaCliente = new Tiendacliente();
        $form = $this->createForm('AppBundle\Form\TiendaClienteType', $tiendaCliente);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tiendaCliente);
            $em->flush($tiendaCliente);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('tiendacliente_index');

        }

        return $this->render('tiendacliente/new.html.twig', array(
            'tiendaCliente' => $tiendaCliente,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a tiendaCliente entity.
     *
     */
    public function showAction(TiendaCliente $tiendaCliente)
    {
        $deleteForm = $this->createDeleteForm($tiendaCliente);

        return $this->render('tiendacliente/show.html.twig', array(
            'tiendaCliente' => $tiendaCliente,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing tiendaCliente entity.
     *
     */
    public function editAction(Request $request, TiendaCliente $tiendaCliente)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($tiendaCliente);
        $editForm = $this->createForm('AppBundle\Form\TiendaClienteType', $tiendaCliente);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('tiendacliente_index');
        }

        return $this->render('tiendacliente/edit.html.twig', array(
            'tiendaCliente' => $tiendaCliente,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a tiendaCliente entity.
     *
     */
    public function deleteAction(Request $request, TiendaCliente $tiendaCliente)
    {
        $form = $this->createDeleteForm($tiendaCliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tiendaCliente);
            $em->flush($tiendaCliente);
        }

        return $this->redirectToRoute('tiendacliente_index');
    }

    /**
     * Creates a form to delete a tiendaCliente entity.
     *
     * @param TiendaCliente $tiendaCliente The tiendaCliente entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TiendaCliente $tiendaCliente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tiendacliente_delete', array('id' => $tiendaCliente->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(TiendaCliente $tiendaCliente)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($tiendaCliente);
        $em->flush($tiendaCliente);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('tiendacliente_index');
    }
}
