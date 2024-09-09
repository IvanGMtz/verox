<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProduccionEmpaque;
use AppBundle\Entity\ProduccionOrden;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Produccionempaque controller.
 *
 */
class ProduccionEmpaqueController extends Controller
{
    /**
     * Lists all produccionEmpaque entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator, ProduccionOrden $produccionOrden)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProduccionEmpaque'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }

        $produccionEmpaquesQ = $em->getRepository('AppBundle:ProduccionEmpaque')
        ->createQueryBuilder('a')
        ->where('a.ordenProduccion = :order')
        ->setParameter('order', $produccionOrden);

        if($q && $q !=''){
          $this->get('session')->set('q.ProduccionEmpaque', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $produccionEmpaquesQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $produccionEmpaquesQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }

        $query = $produccionEmpaquesQ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );

        $produccionEmpaques = $pagination->getItems();
        $confirmados = [];
        $empacados = [];
        $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                ->createQueryBuilder('a')
                ->where('a.ordenProduccion = :order')
                ->setParameter('order', $produccionOrden)
                ->getQuery()
                ->getResult();
        foreach ($tallaP as $key => $item) {
          $llave=$item->getDiseno()->getDiseno()->getNombre();
          if (array_key_exists($llave,$confirmados)) {
            $confirmados[$llave] += $item->getCantidadConfirmada();
          }
          else{
            $confirmados[$llave] = $item->getCantidadConfirmada();
          }
        }
        foreach ($produccionEmpaques as $key => $item) {
          $llave=$item->getDiseno()->getDiseno()->getNombre();
          if (array_key_exists($llave,$empacados)) {
            $empacados[$llave] += $item->getCantidad();
          }
          else{
            $empacados[$llave] = $item->getCantidad();
          }
        }
        return $this->render('produccionempaque/index.html.twig', array(
            'produccionEmpaques' => $produccionEmpaques,
            'q' => $q,
            'pagination' => $pagination,
            'orden_produccion'=>$produccionOrden,
            'empacados' => $empacados,
            'confirmados' => $confirmados
        ));
    }

    /**
     * Creates a new produccionEmpaque entity.
     *
     */
    public function newAction(Request $request,ProduccionOrden $produccionOrden)
    {
        $produccionEmpaque = new Produccionempaque();
        $form = $this->createForm('AppBundle\Form\ProduccionEmpaqueType', $produccionEmpaque);
        $form->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($produccionEmpaque);
            $em->flush($produccionEmpaque);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('produccionempaque_index',['produccionOrden'=>$produccionOrden->getId()]);

        }

        return $this->render('produccionempaque/new.html.twig', array(
            'produccionEmpaque' => $produccionEmpaque,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a produccionEmpaque entity.
     *
     */
    public function showAction(ProduccionEmpaque $produccionEmpaque,ProduccionOrden $produccionOrden)
    {
        $deleteForm = $this->createDeleteForm($produccionEmpaque);

        return $this->render('produccionempaque/show.html.twig', array(
            'produccionEmpaque' => $produccionEmpaque,
            'delete_form' => $deleteForm->createView(),
            'produccionOrden' => $produccionOrden
        ));
    }

    /**
     * Displays a form to edit an existing produccionEmpaque entity.
     *
     */
    public function editAction(Request $request, ProduccionEmpaque $produccionEmpaque,ProduccionOrden $produccionOrden)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($produccionEmpaque);
        $editForm = $this->createForm('AppBundle\Form\ProduccionEmpaqueType', $produccionEmpaque);
        $editForm->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('produccionempaque_index',['produccionOrden'=>$produccionOrden->getId()]);
        }

        return $this->render('produccionempaque/edit.html.twig', array(
            'produccionEmpaque' => $produccionEmpaque,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a produccionEmpaque entity.
     *
     */
    public function deleteAction(Request $request, ProduccionEmpaque $produccionEmpaque)
    {
        $orden = $produccionEmpaque->getOrdenProduccion()->getId();
        $form = $this->createDeleteForm($produccionEmpaque);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produccionEmpaque);
            $em->flush($produccionEmpaque);
        }
        return $this->redirectToRoute('produccionempaque_index',['produccionOrden'=>$orden]);
    }

    /**
     * Creates a form to delete a produccionEmpaque entity.
     *
     * @param ProduccionEmpaque $produccionEmpaque The produccionEmpaque entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProduccionEmpaque $produccionEmpaque)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produccionempaque_delete', array('produccionEmpaque' => $produccionEmpaque->getId(),'produccionOrden' => $produccionEmpaque->getOrdenProduccion())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function eraseAction(ProduccionEmpaque $produccionEmpaque)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($produccionEmpaque);
        $em->flush($produccionEmpaque);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('produccionempaque_index');
    }
}
