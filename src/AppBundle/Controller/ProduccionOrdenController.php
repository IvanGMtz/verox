<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProduccionOrden;
use AppBundle\Entity\InventarioOrden;
use AppBundle\Entity\InventarioOrdenItem;
use AppBundle\Entity\InventarioOrdenNovedad;
use AppBundle\Entity\ProduccionCosto;
use AppBundle\Entity\ProduccionDiseno;
use AppBundle\Entity\DisenoNovedad;
use AppBundle\Entity\ProcesoNombre;
use AppBundle\Entity\Proceso;
use AppBundle\Entity\Producto;
use AppBundle\Entity\ProductoCategoria;
use AppBundle\Entity\ProductoColor;
use AppBundle\Entity\ProductoTalla;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Produccionorden controller.
 *
 */
class ProduccionOrdenController extends Controller
{
    /**
     * Lists all produccionOrden entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProduccionOrden'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }

        $produccionOrdensQ = $em->getRepository('AppBundle:ProduccionOrden')->createQueryBuilder('a');

        if($q && $q !=''){
          $this->get('session')->set('q.ProduccionOrden', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $produccionOrdensQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $produccionOrdensQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }

        $query = $produccionOrdensQ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );

        $produccionOrdens = $pagination->getItems();


        return $this->render('produccionorden/index.html.twig', array(
            'produccionOrdens' => $produccionOrdens,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new produccionOrden entity.
     *
     */
    public function newAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $fecha = new \DateTime();
        $produccionOrden = new Produccionorden();
        $produccionOrden->setReferencia($request->request->get('referencia'));
        $produccionOrden->setFechaCreacion($fecha);
        $produccionOrden->setEstado(0);
        $produccionOrden->setNotas($request->request->get('notas'));
        $em->persist($produccionOrden);
        $em->flush($produccionOrden);

        $params = $request->request->all();
        foreach ($params as $key => $param) {
          if(strpos($key, '_1') !== false ){
            $index = explode("_",$key)[1];
            $procesoNombre = $this->getDoctrine()->getRepository(ProcesoNombre::class)->find($index);
            $produccionCosto = new ProduccionCosto();
            $produccionCosto->setProceso($procesoNombre);
            $produccionCosto->setCosto(floatval($request->request->get('p_'.$index.'_1')));
            $produccionCosto->setCosto2(floatval($request->request->get('p_'.$index.'_2')));
            $produccionCosto->setCosto3(floatval($request->request->get('p_'.$index.'_3')));
            $produccionCosto->setCosto4(floatval($request->request->get('p_'.$index.'_4')));
            $produccionCosto->setOrdenProduccion($produccionOrden);
            $em->persist($produccionCosto);
            $em->flush();
          }
          elseif (strpos($key, 'p-') !== false) {
            $index = explode("-",$key)[1];
            $procesoNombre = $this->getDoctrine()->getRepository(ProcesoNombre::class)->find($index);
            $produccionCosto = new ProduccionCosto();
            $produccionCosto->setProceso($procesoNombre);
            $produccionCosto->setCosto((int)$request->request->get($key));
            $produccionCosto->setOrdenProduccion($produccionOrden);
            $em->persist($produccionCosto);
            $em->flush();
          }
        }

        $this->addFlash(
            'success',
            'Registro creado correctamente'
        );
        return $this->redirectToRoute('produccionorden_show',['id'=>$produccionOrden->getId()]);
    }

    /**
     * Finds and displays a produccionOrden entity.
     *
     */
    public function showAction(ProduccionOrden $produccionOrden)
    {
        try {
          $deleteForm = $this->createDeleteForm($produccionOrden);
          $em = $this->getDoctrine()->getManager();
          $disenosOrden = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $produccionOrden)
                  ->getQuery()
                  ->getResult();
          $disenos = $em->getRepository('AppBundle:Diseno')
                  ->createQueryBuilder('a')
                  ->where('a.estado = :status')
                  ->setParameter('status', 2)
                  ->getQuery()
                  ->getResult();
          $tallas = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $produccionOrden)
                  ->getQuery()
                  ->getResult();
          $costos = $em->getRepository('AppBundle:ProduccionCosto')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $produccionOrden)
                  ->getQuery()
                  ->getResult();
          $novedades = $em->getRepository('AppBundle:ProduccionNovedad')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $produccionOrden)
                  ->getQuery()
                  ->getResult();
          $procesos_disenos = []; 
          $procesos_disenos_finalizados = [];
          $materiales_disenos = [];
          foreach ($disenosOrden as $key => $diseno) {
            $materiales = $em->getRepository('AppBundle:ProduccionCostoMaterial')
                    ->createQueryBuilder('a')
                    ->where('a.diseno = :design')
                    ->setParameter('design', $diseno->getDiseno()->getId())
                    ->andwhere('a.ordenProduccion = :orden')
                    ->setParameter('orden', $produccionOrden)
                    ->getQuery()
                    ->getResult();
            $materiales_disenos[$diseno->getDiseno()->getId()] = $materiales; 
            $procesos = $em->getRepository('AppBundle:Proceso')
                    ->createQueryBuilder('a')
                    ->where('a.diseno = :design')
                    ->setParameter('design', $diseno->getDiseno())
                    ->andwhere('a.tipoOrden = :type')
                    ->setParameter('type', 'PRODUCCION')
                    ->andwhere('a.status = :stat')
                    ->setParameter('stat', 1)
                    ->andwhere('a.orden = :order')
                    ->setParameter('order', $produccionOrden->getId())
                    ->getQuery()
                    ->getResult();
            array_push($procesos_disenos,$procesos);
            $procesos2 = $em->getRepository('AppBundle:Proceso')
                    ->createQueryBuilder('a')
                    ->where('a.diseno = :design')
                    ->setParameter('design', $diseno->getDiseno())
                    ->andwhere('a.tipoOrden = :type')
                    ->setParameter('type', 'PRODUCCION')
                    ->andwhere('a.status = :stat')
                    ->setParameter('stat', 2)
                    ->andwhere('a.orden = :order')
                    ->setParameter('order', $produccionOrden->getId())
                    ->getQuery()
                    ->getResult();
            foreach ($procesos2 as $key => $value) {
              $procesos_disenos_finalizados[$value->getProceso()] = $value->getId();
            }
            #array_push($procesos_disenos_finalizados,$procesos2);
          }
          return $this->render('produccionorden/show.html.twig', array(
              'produccionOrden' => $produccionOrden,
              'delete_form' => $deleteForm->createView(),
              'disenos_orden' => $disenosOrden,
              'disenos' => $disenos,
              'tallas_orden' => $tallas,
              'novedades' => $novedades,
              'procesos_disenos' => $procesos_disenos,
              'procesos_ended' => $procesos_disenos_finalizados,
              'costos' => $costos,
              'materiales' => $materiales_disenos
          ));
        } catch (\Throwable $th) {
          dump($th);exit;
        }
    }

    /**
     * Displays a form to edit an existing produccionOrden entity.
     *
     */
    public function editAction(Request $request, ProduccionOrden $produccionOrden)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($produccionOrden);
        $editForm = $this->createForm('AppBundle\Form\ProduccionOrdenType', $produccionOrden);
        $editForm->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('produccionorden_index');
        }

        return $this->render('produccionorden/edit.html.twig', array(
            'produccionOrden' => $produccionOrden,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a produccionOrden entity.
     *
     */
    public function deleteAction(Request $request, ProduccionOrden $produccionOrden)
    {
        $form = $this->createDeleteForm($produccionOrden);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $procesos = $em->getRepository('AppBundle:Proceso')
            ->createQueryBuilder('a')
            ->where('a.orden = :order')
            ->setParameter('order', $produccionOrden->getId())
            ->andwhere('a.tipoOrden = :tipo')
            ->setParameter('tipo', 'PRODUCCION')->getQuery()
            ->getResult();
           foreach ($procesos as $proceso) {
            $em->remove($proceso);
            $em->flush();
           }

            
            $em->remove($produccionOrden);
            $em->flush($produccionOrden);
        }

        return $this->redirectToRoute('produccionorden_index');
    }

    /**
     * Creates a form to delete a produccionOrden entity.
     *
     * @param ProduccionOrden $produccionOrden The produccionOrden entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProduccionOrden $produccionOrden)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produccionorden_delete', array('id' => $produccionOrden->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function eraseAction(ProduccionOrden $produccionOrden)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($produccionOrden);
        $em->flush($produccionOrden);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('produccionorden_index');
    }

    public function lanzarAction(ProduccionOrden $produccionOrden){
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $em = $this->getDoctrine()->getManager();
      $fecha = new \DateTime();
      $produccionOrden->setEstado(1);
      $disenos = $em->getRepository('AppBundle:ProduccionDiseno')
              ->createQueryBuilder('a')
              ->where('a.ordenProduccion = :order')
              ->setParameter('order', $produccionOrden)
              ->getQuery()
              ->getResult();
      foreach ($disenos as $diseno) {
        if($diseno->getEstado() == 0){
          $diseno->setEstado(1);//Cambiar estado a lanzado
          $em->persist($diseno);
          $em->flush();
          #Crear un producto nuevo si no existe deribado del diseño a producir
          $check_producto = $this->getDoctrine()->getRepository(Producto::class)->findOneBy(array('referencia' => $diseno->getDiseno()->getReferencia()));
          if($check_producto == null){
            #Categoría
            $producto_categoria = $this->getDoctrine()->getRepository(ProductoCategoria::class)->findOneBy(array('nombreEs' => $diseno->getDiseno()->getCategoria()));
            $producto = new Producto();
            $producto->setNombre($diseno->getDiseno()->getNombre());
            $producto->setReferencia($diseno->getDiseno()->getReferencia());
            $producto->setCategoria($producto_categoria);
            $producto->setPrecioDetal(0);
            $producto->setPrecioMayorista(0);
            $producto->setEtiqueta("NEW");
            $producto->setMarca("VEROX");
            $producto->setEstado("INACTIVO");
            $em->persist($producto);
            $em->flush();
            #Crear Color
            $color = new ProductoColor();
            $color->setProducto($producto);
            $em->persist($color);
            $em->flush();
            #crear tallas
            $producto_tallas = $em->getRepository('AppBundle:ProduccionTalla')
            ->createQueryBuilder('a')
            ->where('a.diseno = :design')
            ->setParameter('design', $diseno)
            ->andwhere('a.ordenProduccion = :orden')
            ->setParameter('orden', $produccionOrden)->getQuery()
            ->getResult();
            foreach ($producto_tallas as $talla) {
              $p_talla = new ProductoTalla();
              $p_talla->setNombre($talla->getTalla());
              $p_talla->setProducto($producto);
              $em->persist($p_talla);
              $em->flush();
            }
          }

          $Proceso = new Proceso();
          $Proceso->setDiseno($diseno->getDiseno());
          $Proceso->setCantidad($diseno->getCantidad());
          $Proceso->setProceso('TRAZO');
          $Proceso->setTipoOrden('PRODUCCION');
          $Proceso->setOrden($produccionOrden->getId());
          $Proceso->setFechaInicio($fecha);
          $Proceso->setUserCreacion($user);
          $Proceso->setStatus(1);
          $em->persist($Proceso);
          $em->flush();

          $registro = new DisenoNovedad();
          $registro->setDiseno($diseno->getDiseno());
          $registro->setUsuarioCreacion($user);
          $registro->setFechaCreacion($fecha);
          $registro->setDescripcion('Proceso de TRAZO Iniciado');
          $registro->setRef1('PRODUCCION');
          $registro->setTipo('TRAZO');

          $em->persist($registro);
          $em->flush();

          $inventarioOrden = new InventarioOrden();
          $inventarioOrden->setDepartamentoSolicita('PRODUCCION');
          $inventarioOrden->setUsuarioCreacion($user);
          $inventarioOrden->setFechaCreacion($fecha);
          $inventarioOrden->setEstado(1);
          $inventarioOrden->setTienePendientes(false);
          $inventarioOrden->setDescripcion('Generado para Orden de producción ref: '.$produccionOrden->getReferencia().' ID #'.$produccionOrden->getId().' diseño ref: #'.$diseno->getDiseno()->getReferencia().' '.$diseno->getDiseno()->getNombre());
          $inventarioOrden->setRef1($diseno->getDiseno()->getId());
          $inventarioOrden->setRef2($diseno->getDiseno()->getReferencia());
          $inventarioOrden->setRef3($produccionOrden->getId());

          $em->persist($inventarioOrden);
          $em->flush($inventarioOrden);

          foreach($diseno->getDiseno()->getMateriales() as $material){
            $item = new InventarioOrdenItem();
            $item->setInventarioOrden($inventarioOrden);
            $item->setCantidad($material->getCantidad() * $diseno->getCantidad());
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
          $novedad->setDescripcion('Orden de inventario creada para Orden de producción ref: '.$produccionOrden->getReferencia().' ID #'.$produccionOrden->getId().' diseño ref: #'.$diseno->getDiseno()->getReferencia().' '.$diseno->getDiseno()->getNombre());
          $em->persist($novedad);
          $em->flush($novedad);
        }
      }
      $this->addFlash(
          'success',
          'Orden lanzada correctamente'
      );
      return $this->redirectToRoute('produccionorden_show',['id'=>$produccionOrden->getId()]);
    }

}
