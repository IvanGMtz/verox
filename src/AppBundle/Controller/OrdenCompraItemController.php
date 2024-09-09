<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenCompraItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

use AppBundle\Entity\OrdenCompraNovedad;
use AppBundle\Entity\AlmacenZonaInventario;


/**
 * Ordencompraitem controller.
 *
 */
class OrdenCompraItemController extends Controller
{
    /**
     * Lists all ordenCompraItem entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.OrdenCompraItem'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $ordenCompraItemsQ = $em->getRepository('AppBundle:OrdenCompraItem')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.OrdenCompraItem', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $ordenCompraItemsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $ordenCompraItemsQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $ordenCompraItemsQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $ordenCompraItems = $pagination->getItems();
        

        return $this->render('ordencompraitem/index.html.twig', array(
            'ordenCompraItems' => $ordenCompraItems,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new ordenCompraItem entity.
     *
     */
//    public function newAction(Request $request)
//    {
//        $ordenCompraItem = new Ordencompraitem();
//        $form = $this->createForm('AppBundle\Form\OrdenCompraItemType', $ordenCompraItem);
//        $form->handleRequest($request);
//        
//        $user = $this->container->get('security.token_storage')->getToken()->getUser();
//        $fecha = new \DateTime();
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($ordenCompraItem);
//            $em->flush($ordenCompraItem);
//            $this->addFlash(
//                'success',
//                'Registro creado correctamente'
//            );
//            return $this->redirectToRoute('ordencompraitem_index');
//
//        }
//
//        return $this->render('ordencompraitem/new.html.twig', array(
//            'ordenCompraItem' => $ordenCompraItem,
//            'form' => $form->createView(),
//        ));
//    }
    public function rechazarModalAction(OrdenCompraItem $ordenCompraItem)
    {
      return $this->render('ordencompraitem/modales/rechazar.html.twig', array(
          'ordenCompraItem' => $ordenCompraItem,
      ));
    }
    public function rechazarAction(Request $request, OrdenCompraItem $ordenCompraItem)
    {
        $em = $this->getDoctrine()->getManager();
        $anotaciones = $request->request->get('anotaciones', false);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();
        
        if($ordenCompraItem->getEstado() != 1 || $ordenCompraItem->getOrdenCompra()->getEstado() != 2){
          $this->addFlash(
              'error',
              'El item ya ha sido procesado'
          );
          return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompraItem->getOrdenCompra()->getId()]);
        }
        
        if(!$anotaciones || $anotaciones == ''){
          $this->addFlash(
              'error',
              'Debe ingresar una anotación'
          );
          return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompraItem->getOrdenCompra()->getId()]);
        }
        
        $ordenCompraItem->setEstado(0);
        $em->persist($ordenCompraItem);
        
        $novedad = new OrdenCompraNovedad();
        $novedad->setFechaCreacion($fecha);
        $novedad->setTipo('ITEM RECHAZADO INVENTARIO');
        $novedad->setUsuarioCreacion($user);
        $novedad->setOrdenCompra($ordenCompraItem->getOrdenCompra());
        $novedad->setDescripcion('Item #'.$ordenCompraItem->getId().' -> '.$ordenCompraItem->getMaterial()->getNombre().' ('.$ordenCompraItem->getCantidad().') rechazado de ingreso a inventario');
        $novedad->setAnotaciones($anotaciones);
        $novedad->setTienePendientes(true);
        $em->persist($novedad);
        
        $em->flush();
        
        $ordenCompra = $ordenCompraItem->getOrdenCompra();
        $checkItemsAbiertos = $em->getRepository('AppBundle:OrdenCompraItem')
                ->createQueryBuilder('a')
                ->where('a.ordenCompra = :ordenCompra')
                ->andWhere('a.estado = 1')
                ->setParameter('ordenCompra', $ordenCompra)
                ->getQuery()
                ->getResult()
                ;
        $ordenCompra->setTienePendientes(true);
        $em->persist($ordenCompra);
        if(count($checkItemsAbiertos) == 0){
          $ordenCompra->setEstado(3);
          $ordenCompra->setFechaRecibe($fecha);
          $ordenCompra->setUsuarioRecibe($user);
          $em->persist($ordenCompra);
          
          $novedadCierre = new OrdenCompraNovedad();
          $novedadCierre->setFechaCreacion($fecha);
          $novedadCierre->setTipo('CERRADA');
          $novedadCierre->setUsuarioCreacion($user);
          $novedadCierre->setOrdenCompra($ordenCompraItem->getOrdenCompra());
          $novedadCierre->setDescripcion('Orden de compra cerrada');
          $em->persist($novedadCierre);
        }
        $em->flush();
        
        $this->addFlash(
            'warning',
            'Item rechazado correctamente'
        );
        return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompraItem->getOrdenCompra()->getId()]);
    }
    
    
    public function parcialModalAction(OrdenCompraItem $ordenCompraItem)
    {
      return $this->render('ordencompraitem/modales/parcial.html.twig', array(
          'ordenCompraItem' => $ordenCompraItem,
      ));
    }
    public function parcialAction(Request $request, OrdenCompraItem $ordenCompraItem)
    {
        $em = $this->getDoctrine()->getManager();
        $almacenZonaInventario = new AlmacenZonaInventario();
        if($ordenCompraItem->getEstado() == 3){
          $almacenZonaInventario->setCantidadActual($ordenCompraItem->getCantidad() - $ordenCompraItem->getEnInventario());
        }
        else{
          $almacenZonaInventario->setCantidadActual($ordenCompraItem->getCantidad());
        }
        $form = $this->createForm('AppBundle\Form\AlmacenZonaInventarioType', $almacenZonaInventario, ['blocked' => false]);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();
        if($ordenCompraItem->getEnInventario() + $almacenZonaInventario->getCantidadActual() > $ordenCompraItem->getCantidad()){
          $this->addFlash(
              'error',
              'No puedes ingresar una cantidad mayor al faltante'
          );
          return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompraItem->getOrdenCompra()->getId()]);
        }
        if($ordenCompraItem->getOrdenCompra()->getEstado() != 2){
          $this->addFlash(
              'error',
              'El item ya ha sido cargado a inventario'
          );
          return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompraItem->getOrdenCompra()->getId()]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->get('appbundle_almacenzonainventario', false);
            $anotaciones = $almacenZonaInventario->getAnotaciones();
            $cantidad = $almacenZonaInventario->getCantidadActual();
            // dump($ordenCompraItem->getCantidad(), $cantidad);exit;
            if(!$anotaciones || $anotaciones == ''){
              $this->addFlash(
                  'error',
                  'Debe ingresar una anotación'
              );
              return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompraItem->getOrdenCompra()->getId()]);
            }

            if(!$cantidad || $cantidad == ''){
              $this->addFlash(
                  'error',
                  'Debe ingresar una cantidad'
              );
              return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompraItem->getOrdenCompra()->getId()]);
            }
            
            // $cantidadOriginal = $ordenCompraItem->getCantidad();
            // $ordenCompraItem->setCantidad($cantidad);
            // $em->persist($ordenCompraItem);
            // $em->flush($ordenCompraItem);
            // $em->refresh($ordenCompraItem);
          
            $inventarioService = $this->container->get('app.inventario');
            $inventarioService->agregarInventario($ordenCompraItem, $almacenZonaInventario, $user, false);
            
            // $ordenCompraItem->setCantidad($cantidadOriginal);
            // $em->persist($ordenCompraItem);

            $novedad = new OrdenCompraNovedad();
            $novedad->setFechaCreacion($fecha);
            $novedad->setTipo('ITEM INGRESADO PARCIALMENTE INVENTARIO');
            $novedad->setUsuarioCreacion($user);
            $novedad->setOrdenCompra($ordenCompraItem->getOrdenCompra());
            $novedad->setDescripcion('Item #'.$ordenCompraItem->getId().' -> '.$ordenCompraItem->getMaterial()->getNombre().' ('.$ordenCompraItem->getCantidad().') ingresado parcialmente, cantidad: '.$cantidad);
            $novedad->setAnotaciones($anotaciones);
            $novedad->setTienePendientes(true);
            $em->persist($novedad);

            $em->flush();

            $this->addFlash(
                'warning',
                'Item ingresado parcialmente correctamente'
            );
            return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompraItem->getOrdenCompra()->getId()]);
            
        }

        return $this->render('ordencompraitem/modales/parcial.html.twig', array(
            'almacenZonaInventario' => $almacenZonaInventario,
            'form' => $form->createView(),
            'ordenCompraItem' => $ordenCompraItem
        ));
    }
    
    /**
     * Finds and displays a ordenCompraItem entity.
     *
     */
//    public function showAction(OrdenCompraItem $ordenCompraItem)
//    {
//        $deleteForm = $this->createDeleteForm($ordenCompraItem);
//
//        return $this->render('ordencompraitem/show.html.twig', array(
//            'ordenCompraItem' => $ordenCompraItem,
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

    /**
     * Displays a form to edit an existing ordenCompraItem entity.
     *
     */
//    public function editAction(Request $request, OrdenCompraItem $ordenCompraItem)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $deleteForm = $this->createDeleteForm($ordenCompraItem);
//        $editForm = $this->createForm('AppBundle\Form\OrdenCompraItemType', $ordenCompraItem);
//        $editForm->handleRequest($request);
//        
//        $user = $this->container->get('security.token_storage')->getToken()->getUser();
//        $fecha = new \DateTime();
//
//        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $em->flush();
//            $this->addFlash(
//                'success',
//                'Registro editado correctamente'
//            );
//            return $this->redirectToRoute('ordencompraitem_index');
//        }
//
//        return $this->render('ordencompraitem/edit.html.twig', array(
//            'ordenCompraItem' => $ordenCompraItem,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

    /**
     * Deletes a ordenCompraItem entity.
     *
     */
//    public function inventarioAction(Request $request, OrdenCompraItem $ordenCompraItem)
//    {
//        $form = $this->createDeleteForm($ordenCompraItem);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($ordenCompraItem);
//            $em->flush($ordenCompraItem);
//        }
//
//        return $this->redirectToRoute('ordencompraitem_index');
//    }

    /**
     * Creates a form to delete a ordenCompraItem entity.
     *
     * @param OrdenCompraItem $ordenCompraItem The ordenCompraItem entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createDeleteForm(OrdenCompraItem $ordenCompraItem)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('ordencompraitem_delete', array('id' => $ordenCompraItem->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }
//    
//    public function eraseAction(OrdenCompraItem $ordenCompraItem)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $em->remove($ordenCompraItem);
//        $em->flush($ordenCompraItem);
//        $this->addFlash(
//            'success',
//            'Registro eliminado correctamente'
//        );
//        return $this->redirectToRoute('ordencompraitem_index');
//    }
}
