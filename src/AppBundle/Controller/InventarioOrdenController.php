<?php

namespace AppBundle\Controller;

use AppBundle\Entity\InventarioOrden;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\InventarioOrdenItem;
use AppBundle\Entity\InventarioOrdenNovedad;
use AppBundle\Entity\Diseno;
use AppBundle\Entity\DisenoNovedad;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Inventarioorden controller.
 *
 */
class InventarioOrdenController extends Controller
{
    /**
     * Lists all inventarioOrden entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $q = $request->request->get('q', $this->get('session')->get('q.InventarioOrden'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        $inventarioOrdensQ = $em->getRepository('AppBundle:InventarioOrden')->createQueryBuilder('a');
        if($q && $q !=''){
          $this->get('session')->set('q.InventarioOrden', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $inventarioOrdensQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $inventarioOrdensQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
          if($user->hasRole('')){
            $inventarioOrdensQ->andWhere('a.usuarioCreacion = :user')->setParameter('user', $user);
          }
        }else{
          if($user->hasRole('')){
            $inventarioOrdensQ->where('a.usuarioCreacion = :user')->setParameter('user', $user);
          }
        }
        $inventarioOrdensQ->orderBy('a.fechaCreacion', 'DESC');
        $query = $inventarioOrdensQ->getQuery();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        $inventarioOrdens = $pagination->getItems();
        return $this->render('inventarioorden/index.html.twig', array(
            'inventarioOrdens' => $inventarioOrdens,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN_PRODUCCION') or has_role('ROLE_CORTE')")
     *
     */
    public function pendAceptarAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $q = $request->request->get('q', $this->get('session')->get('q.InventarioOrden'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        $inventarioOrdensQ = $em->getRepository('AppBundle:InventarioOrden')->createQueryBuilder('a');
        if($q && $q !=''){
          $this->get('session')->set('q.InventarioOrden', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $inventarioOrdensQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $inventarioOrdensQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
          $inventarioOrdensQ->andWhere('a.estado = 1');
        }else{
          $inventarioOrdensQ->where('a.estado = 1');
        }
        $inventarioOrdensQ->orderBy('a.fechaCreacion', 'DESC');
        $query = $inventarioOrdensQ->getQuery();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        $inventarioOrdens = $pagination->getItems();
        return $this->render('inventarioorden/index.html.twig', array(
            'inventarioOrdens' => $inventarioOrdens,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN_PRODUCCION') or has_role('ROLE_INVENTARIO') or has_role('ROLE_CORTE')")
     *
     */
    public function pendEntregaAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $q = $request->request->get('q', $this->get('session')->get('q.InventarioOrden'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        $inventarioOrdensQ = $em->getRepository('AppBundle:InventarioOrden')->createQueryBuilder('a');
        if($q && $q !=''){
          $this->get('session')->set('q.InventarioOrden', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $inventarioOrdensQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $inventarioOrdensQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
          $inventarioOrdensQ->andWhere('a.estado = 2');
        }else{
          $inventarioOrdensQ->where('a.estado = 2');
        }
        $inventarioOrdensQ->orderBy('a.fechaCreacion', 'DESC');
        $query = $inventarioOrdensQ->getQuery();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        $inventarioOrdens = $pagination->getItems();
        return $this->render('inventarioorden/index.html.twig', array(
            'inventarioOrdens' => $inventarioOrdens,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new inventarioOrden entity.
     *
     */
    public function newAction(Request $request)
    {
        $inventarioOrden = new Inventarioorden();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();
        $roles = $user->getRoles();
        $departamento = str_replace('ROLE_', '', $roles[0]);
        $inventarioOrden->setDepartamentoSolicita($departamento);

        $form = $this->createForm('AppBundle\Form\InventarioOrdenType', $inventarioOrden);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $inventarioOrden->setUsuarioCreacion($user);
            $inventarioOrden->setFechaCreacion($fecha);
            $inventarioOrden->setEstado(1);
            $inventarioOrden->setTienePendientes(false);
            $inventarioOrden->setDepartamentoSolicita($departamento);

            foreach($inventarioOrden->getItems() as &$item){
              $item->setUsuarioCreacion($user);
              $item->setFechaCreacion($fecha);
              $item->setEstado(1);
            }

            $em->persist($inventarioOrden);
            $em->flush($inventarioOrden);

            $novedad = new InventarioOrdenNovedad();
            $novedad->setFechaCreacion($fecha);
            $novedad->setTipo('CREADA');
            $novedad->setUsuarioCreacion($user);
            $novedad->setInventarioOrden($inventarioOrden);
            $novedad->setDescripcion('Orden de inventario creada');
            $em->persist($novedad);
            $em->flush($novedad);

            $em->flush();
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('inventarioorden_show',['id'=>$inventarioOrden->getId()]);

        }

        return $this->render('inventarioorden/new.html.twig', array(
            'inventarioOrden' => $inventarioOrden,
            'form' => $form->createView(),
        ));
    }

    public function newFromDisenoAction(Diseno $diseno, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();
        $roles = $user->getRoles();

        $inventarioOrden = $em->getRepository('AppBundle:InventarioOrden')
                ->createQueryBuilder('a')
                ->where('a.departamentoSolicita = :depto')
                ->setParameter('depto', 'DESIGN')
                ->andWhere('a.ref1 = :ref1')
                ->setParameter('ref1', $diseno->getId())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
                ;

        if(!is_null($inventarioOrden)){
          $this->addFlash(
              'error',
              'El diseño ya cuenta con una orden de inventario generada'
          );
          return $this->redirectToRoute('diseno_show',['id'=>$diseno->getId()]);
        }

        $inventarioOrden = new Inventarioorden();
        $inventarioOrden->setDepartamentoSolicita('DESIGN');
        $inventarioOrden->setUsuarioCreacion($user);
        $inventarioOrden->setFechaCreacion($fecha);
        $inventarioOrden->setEstado(1);
        $inventarioOrden->setTienePendientes(false);
        $inventarioOrden->setDescripcion('Generado para diseño ref: #'.$diseno->getReferencia().' '.$diseno->getNombre().' ('.$diseno->getId().')');
        $inventarioOrden->setRef1($diseno->getId());
        $inventarioOrden->setRef2($diseno->getReferencia());

        $em->persist($inventarioOrden);
        $em->flush($inventarioOrden);

        if(count($diseno->getMateriales())<1){
          $this->addFlash(
            'error',
            'El diseño no tiene materiales asignados!'
          );
          return $this->redirectToRoute('diseno_show',['id'=>$diseno->getId()]);
        }
        foreach($diseno->getMateriales() as $material){
          $item = new InventarioOrdenItem();
          $item->setInventarioOrden($inventarioOrden);
          $item->setCantidad($material->getCantidad());
          $item->setMaterial($material->getMaterial());
          $item->setUsuarioCreacion($user);
          $item->setFechaCreacion($fecha);
          $item->setEstado(1);
          $em->persist($item);
          $em->flush($item);
        }

        $novedad = new InventarioOrdenNovedad();
        $novedad->setFechaCreacion($fecha);
        $novedad->setTipo('CREADA');
        $novedad->setUsuarioCreacion($user);
        $novedad->setInventarioOrden($inventarioOrden);
        $novedad->setDescripcion('Orden de inventario creada para diseño ref: #'.$diseno->getReferencia().' '.$diseno->getNombre().' ('.$diseno->getId().')');
        $em->persist($novedad);
        $em->flush($novedad);

        $novedad2 = new DisenoNovedad();
        $novedad2->setFechaCreacion($fecha);
        $novedad2->setTipo('ORDEN INVENTARIO');
        $novedad2->setUsuarioCreacion($user);
        $novedad2->setDiseno($diseno);
        $novedad2->setDescripcion('Orden de inventario #'.$inventarioOrden->getId().' creada');
        $em->persist($novedad2);
        $em->flush($novedad2);

        $em->flush();
        $this->addFlash(
            'success',
            'Registro creado correctamente'
        );
        return $this->redirectToRoute('diseno_show',['id'=>$diseno->getId()]);

    }

    /**
     * Finds and displays a inventarioOrden entity.
     *
     */
    public function showAction(InventarioOrden $inventarioOrden)
    {
//        $deleteForm = $this->createDeleteForm($inventarioOrden);
      $em = $this->getDoctrine()->getManager();
      $items = $inventarioOrden->getItems();
      foreach($items as &$item){
        $total_cantidad = 0;
        $checkCantidades = $em->getRepository('AppBundle:InventarioMovimiento')
            ->createQueryBuilder('a')
            ->where('a.ref1 = :inventarioOrden')
            ->setParameter('inventarioOrden', $inventarioOrden->getId())
            ->andWhere('a.ref2 = :item')
            ->setParameter('item', $item->getId())
            ->andWhere('a.tipo = :type')
            ->setParameter('type', "EGRESO")
            ->getQuery()
            ->getResult()
            ;
        foreach ($checkCantidades as $item2) {
          $total_cantidad += $item2->getCantidad();
        }
        $inventarioItem = $em->getRepository('AppBundle:Inventario')->findOneBy(['material' => $item->getMaterial()]);
        $disponible = 0;
        if($inventarioItem){$disponible = $inventarioItem->getCantidadActual() - $inventarioItem->getReserva();}
        $item->disponible = $disponible;
        $item->alcanza = $disponible >= $item->getCantidad() - $total_cantidad;
        $item->qty_entregado = $total_cantidad;
      }
      return $this->render('inventarioorden/show.html.twig', array(
          'inventarioOrden' => $inventarioOrden,
//            'delete_form' => $deleteForm->createView(),
      ));
    }

    /**
     * Displays a form to edit an existing inventarioOrden entity.
     *
     */
    public function editAction(Request $request, InventarioOrden $inventarioOrden)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if(!$user->hasRole('ROLE_DESIGN') && !$user->hasRole('ROLE_SUPER_ADMIN')){
          if($inventarioOrden->getUsuarioCreacion()->getId() != $user->getId()){
            $this->addFlash(
                'error',
                'No tiene permisos para este registro'
            );
            return $this->redirectToRoute('inventarioorden_index');
          }
        }

        /*if($inventarioOrden->getEstado() != 1 || !is_null($inventarioOrden->getFechaRecibe()) || !is_null($inventarioOrden->getUsuarioRecibe())){
          $this->addFlash('error', 'La orden ya ha sido recibida, no se puede editar');
          return $this->redirectToRoute('inventarioorden_show',['id'=>$inventarioOrden->getId()]);
        }*/

        $originalTags = new ArrayCollection();

        foreach ($inventarioOrden->getItems() as $item) {
          $originalTags->add($item);
        }

        $editForm = $this->createForm('AppBundle\Form\InventarioOrdenType', $inventarioOrden);
        $editForm->handleRequest($request);



        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if($inventarioOrden->getEstado()!=2){
              $inventarioOrden->setEstado(1);
            }
            foreach($inventarioOrden->getItems() as &$item){
              $id = $item->getId();
              if($id!=null){
                $target = $em->getRepository(InventarioOrdenItem::class)->find($item->getId());
              }
              else{
                $target=null;
              }
              $item->setUsuarioCreacion($user);
              $item->setFechaCreacion($fecha);
              if($target!=null){
                if($target->getEntregado()){
                  $item->setEstado(2);
                }
                else{
                  $item->setEstado(1);
                }
              }
              else{
                $item->setEstado(1);
              }
            }
            foreach ($originalTags as $item) {
                if (false === $inventarioOrden->getItems()->contains($item)) {
                  $item->setInventarioOrden(null);
                  $em->remove($item);
                  $em->flush();
                }
            }
            $em->persist($inventarioOrden);

            $novedad = new InventarioOrdenNovedad();
            $novedad->setFechaCreacion($fecha);
            $novedad->setTipo('EDITADA');
            $novedad->setUsuarioCreacion($user);
            $novedad->setInventarioOrden($inventarioOrden);
            $novedad->setDescripcion('Orden de inventario editada');
            $em->persist($novedad);

            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('inventarioorden_show',['id'=>$inventarioOrden->getId()]);
        }

        return $this->render('inventarioorden/edit.html.twig', array(
            'inventarioOrden' => $inventarioOrden,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN_PRODUCCION')")
     *
     */
    public function aceptarAction(InventarioOrden $inventarioOrden)
    {
        if($inventarioOrden->getEstado() != 1){
          $this->addFlash(
              'error',
              'Orden de inventario ya está en otro proceso'
          );
          return $this->redirectToRoute('inventarioorden_show', ['id' => $inventarioOrden->getId()]);
        }

        $em = $this->getDoctrine()->getManager();

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        $inventarioOrden->setFechaAceptacion($fecha);
        $inventarioOrden->setUsuarioAceptacion($user);
        $inventarioOrden->setEstado(2);

        $novedad = new InventarioOrdenNovedad();
        $novedad->setFechaCreacion($fecha);
        $novedad->setTipo('ACEPTADA');
        $novedad->setUsuarioCreacion($user);
        $novedad->setInventarioOrden($inventarioOrden);
        $novedad->setDescripcion('Orden de inventario aceptada');
        $em->persist($novedad);

        $em->persist($inventarioOrden);
        $em->flush($inventarioOrden);
        $this->addFlash(
            'success',
            'Orden de inventario aceptada correctamente'
        );
        return $this->redirectToRoute('inventarioorden_show', ['id' => $inventarioOrden->getId()]);
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN_PRODUCCION')")
     *
     */
    public function rechazarAction(InventarioOrden $inventarioOrden)
    {
        if($inventarioOrden->getEstado() != 1 && $inventarioOrden->getEstado() != 2){
          $this->addFlash(
              'error',
              'Orden de inventario ya está en otro proceso'
          );
          return $this->redirectToRoute('inventarioorden_show', ['id' => $inventarioOrden->getId()]);
        }

        $enabled = true;
        $items = $inventarioOrden->getItems();
        foreach($items as $item){
          if($item->getEstado() != 1){$enabled = false;}
        }

        if(!$enabled){
          $this->addFlash(
              'warning',
              'Hay items que ya han sido procesados'
          );
          return $this->redirectToRoute('inventarioorden_show', ['id' => $inventarioOrden->getId()]);
        }

        $em = $this->getDoctrine()->getManager();

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        $inventarioOrden->setFechaAceptacion($fecha);
        $inventarioOrden->setUsuarioAceptacion($user);
        $inventarioOrden->setEstado(0);

        $novedad = new InventarioOrdenNovedad();
        $novedad->setFechaCreacion($fecha);
        $novedad->setTipo('RECHAZADA');
        $novedad->setUsuarioCreacion($user);
        $novedad->setInventarioOrden($inventarioOrden);
        $novedad->setDescripcion('Orden de inventario rechazada');
        $em->persist($novedad);

        $em->persist($inventarioOrden);
        $em->flush($inventarioOrden);
        $this->addFlash(
            'success',
            'Orden de inventario rechazada correctamente'
        );
        return $this->redirectToRoute('inventarioorden_show', ['id' => $inventarioOrden->getId()]);
    }

    /**
     * Deletes a inventarioOrden entity.
     *
     */
//    public function deleteAction(Request $request, InventarioOrden $inventarioOrden)
//    {
//        $form = $this->createDeleteForm($inventarioOrden);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($inventarioOrden);
//            $em->flush($inventarioOrden);
//        }
//
//        return $this->redirectToRoute('inventarioorden_index');
//    }

    /**
     * Creates a form to delete a inventarioOrden entity.
     *
     * @param InventarioOrden $inventarioOrden The inventarioOrden entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createDeleteForm(InventarioOrden $inventarioOrden)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('inventarioorden_delete', array('id' => $inventarioOrden->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }

//    public function eraseAction(InventarioOrden $inventarioOrden)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $em->remove($inventarioOrden);
//        $em->flush($inventarioOrden);
//        $this->addFlash(
//            'success',
//            'Registro eliminado correctamente'
//        );
//        return $this->redirectToRoute('inventarioorden_index');
//    }
}
