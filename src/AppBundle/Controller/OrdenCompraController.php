<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenCompra;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\OrdenCompraNovedad;
use AppBundle\Entity\OrdenCompraItem;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Ordencompra controller.
 *
 */
class OrdenCompraController extends Controller
{
    /**
     * Lists all ordenCompra entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.OrdenCompra'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }

        $ordenComprasQ = $em->getRepository('AppBundle:OrdenCompra')->createQueryBuilder('a');

        if($q && $q !=''){
          $this->get('session')->set('q.OrdenCompra', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $ordenComprasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $ordenComprasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        $ordenComprasQ->orderBy('a.fechaCreacion', 'DESC');
        $query = $ordenComprasQ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );

        $ordenCompras = $pagination->getItems();


        return $this->render('ordencompra/index.html.twig', array(
            'ordenCompras' => $ordenCompras,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN_PRODUCCION')")
     *
     */
    public function pendAceptarAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.OrdenCompra'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        $ordenComprasQ = $em->getRepository('AppBundle:OrdenCompra')->createQueryBuilder('a');
        if($q && $q !=''){
          $this->get('session')->set('q.OrdenCompra', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $ordenComprasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $ordenComprasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
          $ordenComprasQ->andWhere('a.estado = 1');
        }else{
          $ordenComprasQ->where('a.estado = 1');
        }
        $ordenComprasQ->orderBy('a.fechaCreacion', 'DESC');
        $query = $ordenComprasQ->getQuery();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        $ordenCompras = $pagination->getItems();
        return $this->render('ordencompra/index.html.twig', array(
            'ordenCompras' => $ordenCompras,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_INVENTARIO') or has_role('ROLE_ADMIN_PRODUCCION') or has_role('ROLE_CORTE')")
     *
     */
    public function pendRecibirAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.OrdenCompra'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        $ordenComprasQ = $em->getRepository('AppBundle:OrdenCompra')->createQueryBuilder('a');
        if($q && $q !=''){
          $this->get('session')->set('q.OrdenCompra', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $ordenComprasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $ordenComprasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
          $ordenComprasQ->andWhere('a.estado = 2');
        }else{
          $ordenComprasQ->where('a.estado = 2');
        }
        $ordenComprasQ->orderBy('a.fechaCreacion', 'DESC');
        $query = $ordenComprasQ->getQuery();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        $ordenCompras = $pagination->getItems();
        return $this->render('ordencompra/index.html.twig', array(
            'ordenCompras' => $ordenCompras,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new ordenCompra entity.
     *
     */
    public function newAction(Request $request)
    {
        $ordenCompra = new Ordencompra();

        $form = $this->createForm('AppBundle\Form\OrdenCompraType', $ordenCompra);
        $form->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ordenCompra->setUsuarioCreacion($user);
            $ordenCompra->setFechaCreacion($fecha);
            $ordenCompra->setEstado(1);
            $ordenCompra->setValorSaldo($ordenCompra->getValorTotal());

            foreach($ordenCompra->getItems() as &$item){
              $item->setUsuarioCreacion($user);
              $item->setFechaCreacion($fecha);
              $item->setEstado(1);
            }

            $em->persist($ordenCompra);
            $em->flush($ordenCompra);

            $novedad = new OrdenCompraNovedad();
            $novedad->setFechaCreacion($fecha);
            $novedad->setTipo('CREADA');
            $novedad->setUsuarioCreacion($user);
            $novedad->setOrdenCompra($ordenCompra);
            $novedad->setDescripcion('Orden de compra creada');
            $em->persist($novedad);
            $em->flush($novedad);

            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompra->getId()]);

        }

        return $this->render('ordencompra/new.html.twig', array(
            'ordenCompra' => $ordenCompra,
            'form' => $form->createView(),
            'now' => $fecha
        ));
    }

    /**
     * Finds and displays a ordenCompra entity.
     *
     */
    public function showAction(OrdenCompra $ordenCompra)
    {
      //        $deleteForm = $this->createDeleteForm($ordenCompra);
      $em = $this->getDoctrine()->getManager();
        $pagos = $em->getRepository('AppBundle:OrdenCompraPago')
        ->createQueryBuilder('a')
        ->where('a.ordenCompra = :ordenCompra')
        ->andWhere('a.estado = 1')
        ->setParameter('ordenCompra', $ordenCompra)
        ->getQuery()
        ->getResult()
        ;
        return $this->render('ordencompra/show.html.twig', array(
            'ordenCompra' => $ordenCompra,
            'pago' => (count($pagos)>0) ? "true" : "false",
      //            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ordenCompra entity.
     *
     */
    public function editAction(Request $request, OrdenCompra $ordenCompra)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if(!$user->hasRole('ROLE_DESIGN') && !$user->hasRole('ROLE_ADMIN_PRODUCCION')){
          if($ordenCompra->getUsuarioCreacion()->getId() != $user->getId()){
            $this->addFlash(
                'error',
                'No tiene permisos para este registro'
            );
            return $this->redirectToRoute('ordencompra_index');
          }
        }

        if($ordenCompra->getEstado() != 1 || !is_null($ordenCompra->getFechaRecibe()) || !is_null($ordenCompra->getUsuarioRecibe())){
          $this->addFlash('error', 'La orden ya ha sido recibida, no se puede editar');
          return $this->redirectToRoute('ordencompra_show',['id'=>$ordenCompra->getId()]);
        }

        $originalTags = new ArrayCollection();

        foreach ($ordenCompra->getItems() as $item) {
          $originalTags->add($item);
        }

        $editForm = $this->createForm('AppBundle\Form\OrdenCompraType', $ordenCompra);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            //            $ordenCompra->setEstado(1);

            foreach($ordenCompra->getItems() as &$item){
              $item->setUsuarioCreacion($user);
              $item->setFechaCreacion($fecha);
              $item->setEstado(1);
            }
            foreach ($originalTags as $item) {
                if (false === $ordenCompra->getItems()->contains($item)) {
                  $item->setOrdenCompra(null);
                  $em->remove($item);
                  $em->flush();
                }
            }
            $pagos = $ordenCompra->getPagos();
            $valorPagado = 0;
            foreach($pagos as $pago){
              if($pago->getEstado() == 1){
                $valorPagado += $pago->getValor();
              }
            }
            $ordenCompra->setValorPagado($valorPagado);
            $ordenCompra->setValorSaldo($ordenCompra->getValorTotal()-$valorPagado);
            if($ordenCompra->getValorSaldo() < 0){$ordenCompra->setValorSaldo(0);}

            if($ordenCompra->getValorSaldo() <= 0){
              $ordenCompra->setPagada(true);
              $ordenCompra->setFechaPagada($fecha);
            }else{
              $ordenCompra->setPagada(false);
              $ordenCompra->setFechaPagada(null);
            }

            $em->persist($ordenCompra);

            $novedad = new OrdenCompraNovedad();
            $novedad->setFechaCreacion($fecha);
            $novedad->setTipo('EDITADA');
            $novedad->setUsuarioCreacion($user);
            $novedad->setOrdenCompra($ordenCompra);
            $novedad->setDescripcion('Orden de compra editada');
            $em->persist($novedad);

            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompra->getId()]);
        }

        return $this->render('ordencompra/edit.html.twig', array(
            'ordenCompra' => $ordenCompra,
            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
            'now' => $ordenCompra->getFechaCreacion()
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN_PRODUCCION')")
     *
     */
    public function aceptarAction(OrdenCompra $ordenCompra)
    {
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $fecha = new \DateTime();

      if($ordenCompra->getEstado() != 1){
        $this->addFlash('error', 'Orden ya está en proceso');
        return $this->redirectToRoute('ordencompra_show',['id'=>$ordenCompra->getId()]);
      }

      $novedad = new OrdenCompraNovedad();
      $novedad->setFechaCreacion($fecha);
      $novedad->setTipo('ACEPTADA');
      $novedad->setUsuarioCreacion($user);
      $novedad->setOrdenCompra($ordenCompra);
      $novedad->setDescripcion('Orden de compra aceptada');
      $em->persist($novedad);

      $ordenCompra->setFechaAceptacion($fecha);
      $ordenCompra->setUsuarioAceptacion($user);
      $ordenCompra->setEstado(2);
      $em->persist($ordenCompra);

      $em->flush();
      $this->addFlash('success', 'Orden de compra aceptada');
      return $this->redirectToRoute('ordencompra_show',['id'=>$ordenCompra->getId()]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN_PRODUCCION') or has_role('ROLE_SUPER_ADMIN')")
     *
     */
    public function rechazarAction(OrdenCompra $ordenCompra)
    {
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $fecha = new \DateTime();

      if($ordenCompra->getEstado() == 0){
        $this->addFlash('error', 'Orden ya ha sido rechazada');
        return $this->redirectToRoute('ordencompra_show',['id'=>$ordenCompra->getId()]);
      }

      $pagos = $em->getRepository('AppBundle:OrdenCompraPago')
              ->createQueryBuilder('a')
              ->where('a.ordenCompra = :ordenCompra')
              ->andWhere('a.estado = 1')
              ->setParameter('ordenCompra', $ordenCompra)
              ->getQuery()
              ->getResult()
              ;
      if(count($pagos)){
        $this->addFlash('error', 'Hay pagos realizados sobre ésta orden, debe cancelarlos primero');
        return $this->redirectToRoute('ordencompra_show',['id'=>$ordenCompra->getId()]);
      }

      $novedad = new OrdenCompraNovedad();
      $novedad->setFechaCreacion($fecha);
      $novedad->setTipo('RECHAZADA');
      $novedad->setUsuarioCreacion($user);
      $novedad->setOrdenCompra($ordenCompra);
      $novedad->setDescripcion('Orden de compra rechazada');
      $em->persist($novedad);

      $ordenCompra->setFechaAceptacion($fecha);
      $ordenCompra->setUsuarioAceptacion($user);
      $ordenCompra->setEstado(0);
      $em->persist($ordenCompra);

      $em->flush();
      $this->addFlash('success', 'Orden de compra rechazada');
      return $this->redirectToRoute('ordencompra_show',['id'=>$ordenCompra->getId()]);
    }

    /**
     * Deletes a ordenCompra entity.
     *
     */
//    public function deleteAction(Request $request, OrdenCompra $ordenCompra)
//    {
//        $form = $this->createDeleteForm($ordenCompra);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($ordenCompra);
//            $em->flush($ordenCompra);
//        }
//
//        return $this->redirectToRoute('ordencompra_index');
//    }

    /**
     * Creates a form to delete a ordenCompra entity.
     *
     * @param OrdenCompra $ordenCompra The ordenCompra entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createDeleteForm(OrdenCompra $ordenCompra)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('ordencompra_delete', array('id' => $ordenCompra->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }

//    public function eraseAction(OrdenCompra $ordenCompra)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $em->remove($ordenCompra);
//        $em->flush($ordenCompra);
//        $this->addFlash(
//            'success',
//            'Registro eliminado correctamente'
//        );
//        return $this->redirectToRoute('ordencompra_index');
//    }
}
