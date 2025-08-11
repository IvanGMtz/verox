<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DespachoOrden;
use AppBundle\Entity\DespachoOrdenItem;
use AppBundle\Entity\ProductoInventarioMovimiento;
use AppBundle\Entity\ProductoInventario;
use AppBundle\Entity\ProductoColor;
use AppBundle\Entity\ProductoTalla;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Despachoorden controller.
 *
 */
class DespachoOrdenController extends Controller
{
    /**
     * Lists all despachoOrden entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $q = $request->request->get('q', $this->get('session')->get('q.DespachoOrden'));
            
            if($q!=null){
                $q['nombre'] = (key_exists('nombre',$q)?$q['nombre']:"");
                $q['apellidos'] = (key_exists('apellidos',$q)?$q['apellidos']:"");
            }
            
            if ($request->isMethod('POST')) {
                $page = 1;
            }else{
                $page = $request->query->getInt('page', 1);
            }
            
            $despachoOrdensQ = $em->getRepository('AppBundle:DespachoOrden')->createQueryBuilder('a')->innerJoin('a.clienteId','b','WITH','b.id = a.clienteId');
            
            if($q && $q !=''){
                $this->get('session')->set('q.DespachoOrden', $q);
                $qcount = 0;
                foreach($q as $field => $value){
                    if($value && $field=='clienteId'){
                        $user = $em->getRepository('AppBundle:StoreUsuarios')->findOneBy(array('email' => $value));
                        if($qcount == 0){
                            $despachoOrdensQ->where('a.'.$field.' = :'.$field)->setParameter($field, ($user)?$user->getId():"");
                        }else{
                            $despachoOrdensQ->andWhere('a.'.$field.' = :'.$field)->setParameter($field, ($user)?$user->getId():"");
                        }
                    }
                    else if($value && $field=='nombre'){
                        if($qcount == 0){
                            $despachoOrdensQ->where('b.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                        }else{
                            $despachoOrdensQ->andWhere('b.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                        }
                    }
                    else if($value && $field=='apellidos'){
                        if($qcount == 0){
                            $despachoOrdensQ->where('b.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                        }else{
                            $despachoOrdensQ->andWhere('b.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                        }
                    }
                    else if($value && $field=='statusPago'){
                        $statusValue = 0;
                        if(strtoupper($value)=='CONFIRMADO' || strtoupper($value)=='PAGADO'){
                            $statusValue = 2;
                        } elseif(strtoupper($value)=='PENDIENTE' || strtoupper($value)=='POR PAGAR'){
                            $statusValue = 1;
                        } elseif(strtoupper($value)=='ABONADO'){
                            $statusValue = 3; // NUEVO: Filtro por estado abonado
                        }
                        
                        if($qcount == 0){
                            $despachoOrdensQ->where('a.'.$field.' = :'.$field)->setParameter($field, $statusValue);
                        }else{
                            $despachoOrdensQ->andWhere('a.'.$field.' = :'.$field)->setParameter($field, $statusValue);
                        }
                    }
                    else if($value && $field=='statusOrden'){
                        if($qcount == 0){
                            $despachoOrdensQ->where('a.'.$field.' = :'.$field)->setParameter($field, (strtoupper($value)=='DESPACHADA')?2:((strtoupper($value)=='POR DESPACHAR')?1:0));
                        }else{
                            $despachoOrdensQ->andWhere('a.'.$field.' = :'.$field)->setParameter($field, (strtoupper($value)=='DESPACHADA')?2:((strtoupper($value)=='POR DESPACHAR')?1:0));
                        }
                    }
                    else if($value){
                        if($qcount == 0){
                            $despachoOrdensQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                        }else{
                            $despachoOrdensQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                        }
                    }
                    $qcount++;
                }
            }
            
            $query = $despachoOrdensQ->orderBy('a.id', 'DESC')->getQuery();
            $pagination = $paginator->paginate($query, $page, 50);
            $despachoOrdens = $pagination->getItems();

            return $this->render('despachoorden/index.html.twig', array(
                'despachoOrdens' => $despachoOrdens,
                'q' => $q,
                'pagination' => $pagination
            ));
        } catch (\Throwable $th) {
            dump($th);exit;
        }
    }

    /**
     * Creates a new despachoOrden entity.
     *
     */
    public function newAction(Request $request)
    {
        try {
            $despachoOrden = new Despachoorden();
            $duplicateData = null;
            
            // Verificar si se está duplicando una orden
            if ($request->query->has('duplicate')) {
                $duplicateId = $request->query->get('duplicate');
                $originalOrder = $this->getDoctrine()->getRepository('AppBundle:Despachoorden')->find($duplicateId);
                
                if ($originalOrder && $originalOrder->getFechaAnulacion() !== null) {
                    // Prellenar datos del cliente
                    $despachoOrden->setClienteId($originalOrder->getClienteId());
                    $despachoOrden->setDireccionEnvio($originalOrder->getDireccionEnvio());
                    $despachoOrden->setClienteTipo($originalOrder->getClienteTipo());
                    $despachoOrden->setTipoPago($originalOrder->getTipoPago());
                    $despachoOrden->setCostoEnvio($originalOrder->getCostoEnvio());
                    
                    // Preparar datos de productos para JavaScript
                    $duplicateData = [
                        'clienteData' => [
                            'clienteId' => $originalOrder->getClienteId(),
                            'clienteTipo' => $originalOrder->getClienteTipo(),
                            'direccionEnvio' => $originalOrder->getDireccionEnvio(),
                            'tipoPago' => $originalOrder->getTipoPago(),
                            'costoEnvio' => $originalOrder->getCostoEnvio()
                        ],
                        'productos' => []
                    ];
                    
                    // Obtener productos de la orden original
                    $items = $this->getDoctrine()->getRepository('AppBundle:DespachoOrdenItem')
                        ->findBy(['ordenDespacho' => $originalOrder]);
                    
                    foreach ($items as $item) {
                        $duplicateData['productos'][] = [
                            'referencia' => $item->getProducto()->getProducto()->getReferencia(),
                            'talla_id' => $item->getProducto()->getId(),
                            'talla_nombre' => $item->getProducto()->getNombre(),
                            'color' => $item->getColor(),
                            'precio' => $item->getPrecio(),
                            'cantidad' => $item->getCantidad(),
                            'producto_id' => $item->getProducto()->getProducto()->getId(),
                            'producto_nombre' => $item->getProducto()->getProducto()->getNombre()
                        ];
                    }
                }
            }
            
            $form = $this->createForm('AppBundle\Form\DespachoOrdenType', $despachoOrden);
            $form->handleRequest($request);
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $fecha = new \DateTime();
            
            if ($form->isSubmitted() && $form->isValid()) {

                if ($despachoOrden->getStatusPago() == 3 && !$despachoOrden->tieneAbonos()) {
                    $this->addFlash('error', 'No se puede crear una orden con estado "Abonado" sin registrar abonos.');
                    return $this->render('despachoorden/new.html.twig', array(
                        'despachoOrden' => $despachoOrden,
                        'form' => $form->createView(),
                        'now' => $fecha,
                        'duplicateData' => $duplicateData
                    ));
                }

                if ($despachoOrden->getStatusPago() == 2) {
                    if (!$despachoOrden->getFechaPago()) {
                        $despachoOrden->setFechaPago(new \DateTime());
                    }
                }

                $productos_check = true;
                $producto_invalido=""; 
                foreach ($request->request as $key => $value) {
                    if(strpos($key,"producto")!== false){
                        $product_id = explode("-",$key)[1];
                        $counter = explode("-",$key)[2];
                        $producto = $this->getDoctrine()->getRepository(ProductoTalla::class)->find($product_id);
                        $em = $this->getDoctrine()->getManager();
                        //dump($product_id,$producto->getNombre(),$request->request->get('color-'.$product_id.'-'.$counter));exit;
                        $product = $em->getRepository('AppBundle:ProductoInventario')->createQueryBuilder('a')
                        ->join('a.producto','p')->join('a.color','c')->join('p.producto','p2')
                        ->where('p2.id = :id')->setParameter('id', $producto->getProducto()->getId())
                        ->andWhere('p.nombre = :talla')->setParameter('talla', $producto->getNombre())
                        ->andWhere('c.nombre = :color')->setParameter('color', $request->request->get('color-'.$product_id.'-'.$counter))
                        ->getQuery()
                        ->getOneOrNullResult();
                        $reservados = $em->getRepository('AppBundle:DespachoOrdenItem')
                            ->createQueryBuilder('a')
                            ->where('a.producto = :producto')
                            ->setParameter('producto', $product->getProducto()->getId())
                            ->andwhere('a.color = :color')
                            ->setParameter('color', $request->request->get('color-'.$product_id.'-'.$counter))
                            ->andwhere('a.estatus = :estatus')
                            ->setParameter('estatus', 1)
                            ->getQuery()
                            ->getResult();
                        $reserva_detal = 0;
                        $reserva_mayorista = 0;
                        foreach ($reservados as $reservado) {
                            if($reservado->getBodega()=="DETAL"){
                                $reserva_detal = $reserva_detal + $reservado->getCantidad();
                            }
                            else{
                                $reserva_mayorista = $reserva_mayorista + $reservado->getCantidad();
                            }
                        }
                        if($product != null){
                            if($despachoOrden->getClienteTipo()=='DETAL' && (int)$product->getQtyActualDetal() - $reserva_detal < (int)$value){
                                $productos_check = false;
                                $producto_invalido = $producto->getProducto()->getReferencia().' - '.$producto->getNombre().' - '.$request->request->get('color-'.$product_id.'-'.$counter);
                            }
                            else if($despachoOrden->getClienteTipo()=='MAYORISTA' && (int)$product->getQtyActualMayorista() - $reserva_mayorista < (int)$value){
                                $productos_check = false;
                                $producto_invalido = $producto->getProducto()->getReferencia().' - '.$producto->getNombre().' - '.$request->request->get('color-'.$product_id.'-'.$counter);
                            }
                        }
                        else{
                            $this->addFlash(
                                'error',
                                'No existe en inventario la referencia '.$producto->getProducto()->getReferencia().' - '.$producto->getNombre().' - '.$request->request->get('color-'.$product_id.'-'.$counter)
                            );
                            return $this->redirectToRoute('despachoorden_index');
                        }
                    }
                }
                if($productos_check){
                    $em = $this->getDoctrine()->getManager();
                    $despachoOrden->setFechaCreacion($fecha);
                    $despachoOrden->setUsuarioCreacion($user);
                    $em->persist($despachoOrden);
                    $em->flush($despachoOrden);
                    foreach ($request->request as $key => $value) {
                        if(strpos($key,"producto")!== false){ 
                            $product_id = explode("-",$key)[1];
                            $counter = explode("-",$key)[2];    
                            $producto = $this->getDoctrine()->getRepository(ProductoTalla::class)->find($product_id);
                            $orden_item = new DespachoOrdenItem();
                            $orden_item->setProducto($producto);
                            $orden_item->setColor($request->request->get('color-'.$product_id.'-'.$counter));
                            $orden_item->setOrdenDespacho($despachoOrden);
                            $orden_item->setCantidad($value);
                            $orden_item->setPrecio($request->request->get('precio-'.$product_id.'-'.$counter));
                            $orden_item->setBodega($request->request->get('appbundle_despachoorden')['clienteTipo']);
                            $orden_item->setEstatus(1);
                            $em->persist($orden_item);
                            $em->flush();
                        }
                    }
                }
                else{
                    $this->addFlash(
                        'error',
                        'El producto '.$producto_invalido.' No tiene la disponibilidad suficiente en el inventario'
                    );
                    return $this->redirectToRoute('despachoorden_index');
                }
                $this->addFlash(
                    'success',
                    'Registro creado correctamente'
                );
                return $this->redirectToRoute('despachoorden_index');

            }
            return $this->render('despachoorden/new.html.twig', array(
                'despachoOrden' => $despachoOrden,
                'form' => $form->createView(),
                'now' => $fecha,
                'duplicateData' => $duplicateData
            ));
        } catch (\Throwable $th) {
            dump($th);exit;
        }
    }

    /**
     * Finds and displays a despachoOrden entity.
     *
     */
    public function showAction(DespachoOrden $despachoOrden)
    {
        $deleteForm = $this->createDeleteForm($despachoOrden);
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        
        $items = $em->getRepository('AppBundle:DespachoOrdenItem')
            ->createQueryBuilder('a')
            ->where('a.ordenDespacho = :orden')
            ->setParameter('orden', $despachoOrden)
            ->getQuery()
            ->getResult();

        $infoAbonos = [
            'total_abonos' => $despachoOrden->getTotalAbonos(),
            'saldo_pendiente' => $despachoOrden->getSaldoPendiente(),
            'puede_abonar' => $despachoOrden->getStatusPago() != 2 && $despachoOrden->getSaldoPendiente() > 0.01,
            'abonos_completos' => $despachoOrden->getAbono1() !== null && $despachoOrden->getAbono2() !== null,
            'puede_completar_pago' => $despachoOrden->getStatusPago() == 3 && $despachoOrden->getSaldoPendiente() > 0.01
        ];

        return $this->render('despachoorden/show.html.twig', array(
            'despachoOrden' => $despachoOrden,
            'delete_form' => $deleteForm->createView(),
            'items' => $items,
            'user' => strtoupper($user->getName()),
            'info_abonos' => $infoAbonos
        ));
    }

    /**
     * Displays a form to edit an existing despachoOrden entity.
     */
    public function editAction(Request $request, DespachoOrden $despachoOrden)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $previousStat = $despachoOrden->getStatusOrden();
            $previousPaymentStatus = $despachoOrden->getStatusPago();
            $deleteForm = $this->createDeleteForm($despachoOrden);
            $editForm = $this->createForm('AppBundle\Form\DespachoOrdenType', $despachoOrden);
            $editForm->handleRequest($request);
            $array_product = [];
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $fecha = new \DateTime();
            
            $items = $em->getRepository('AppBundle:DespachoOrdenItem')
                ->createQueryBuilder('a')
                ->where('a.ordenDespacho = :orden')
                ->setParameter('orden', $despachoOrden)
                ->getQuery()
                ->getResult();
                
            $colors = [];
            foreach ($items as $item) {
                $colores = $em->getRepository('AppBundle:ProductoColor')
                    ->createQueryBuilder('a')
                    ->where('a.producto = :producto')
                    ->setParameter('producto', $item->getProducto()->getProducto()->getId())
                    ->getQuery()
                    ->getResult();
                    
                foreach ($colores as $color) {
                    $existencia = $em->getRepository('AppBundle:ProductoInventario')->findOneBy(['producto' => $item->getProducto(), 'color' => $color]);
                    $reserva_detal = 0;
                    $reserva_mayorista = 0;
                    $reservados = $em->getRepository('AppBundle:DespachoOrdenItem')
                        ->createQueryBuilder('a')
                        ->where('a.producto = :producto')
                        ->setParameter('producto', $item->getProducto())
                        ->andwhere('a.color = :color')
                        ->setParameter('color', $color->getNombre())
                        ->andwhere('a.estatus = :estatus')
                        ->setParameter('estatus', 1)
                        ->getQuery()
                        ->getResult();
                        
                    foreach ($reservados as $reservado) {
                        if ($reservado->getBodega() == "DETAL") {
                            $reserva_detal = $reserva_detal + $reservado->getCantidad();
                        } else {
                            $reserva_mayorista = $reserva_mayorista + $reservado->getCantidad();
                        }
                    }
                    
                    $qtyMayoristas = 0;
                    if ($existencia != null) {
                        $qtyMayoristas = ($existencia->getQtyActualMayorista() - $reserva_mayorista < 0) ? 0 : $existencia->getQtyActualMayorista() - $reserva_mayorista;
                    }
                    
                    array_push($colors, [
                        "nombre" => $color->getNombre(),
                        "hex" => $color->getHex(),
                        "producto_id" => $item->getProducto()->getId(),
                        "DETAL" => ($existencia != null) ? (($qtyMayoristas <= 0) ? 0 : $existencia->getQtyActualDetal() - $reserva_detal) : 0,
                        "MAYORISTA" => ($existencia != null) ? $existencia->getQtyActualMayorista() - $reserva_mayorista : 0,
                    ]);
                }
            }
            
            foreach ($request->request as $key => $param) {
                if (strpos($key, "producto") !== false && count(explode("-", $key)) == 3) {
                    array_push($array_product, explode("-", $key)[1]);
                }
            }
            
            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $newPaymentStatus = $despachoOrden->getStatusPago();
                
                // NUEVA LÓGICA: Manejo mejorado de estados de pago con abonos
                if ($previousPaymentStatus != $newPaymentStatus) {
                    // Si cambia de no pagado/abonado a pagado
                    if (($previousPaymentStatus == 1 || $previousPaymentStatus == 3) && $newPaymentStatus == 2) {
                        if (!$despachoOrden->getFechaPago()) {
                            $despachoOrden->setFechaPago(new \DateTime());
                        }
                    }
                    // Si cambia de pagado a no pagado/abonado
                    elseif ($previousPaymentStatus == 2 && ($newPaymentStatus == 1 || $newPaymentStatus == 3)) {
                        // Solo limpiar fecha de pago si no hay abonos registrados
                        if (!$despachoOrden->tieneAbonos()) {
                            $despachoOrden->setFechaPago(null);
                        }
                    }
                    // Validación: no se puede cambiar a "Abonado" manualmente si no hay abonos
                    elseif ($newPaymentStatus == 3 && !$despachoOrden->tieneAbonos()) {
                        $this->addFlash('error', 'No se puede establecer el estado como "Abonado" sin registrar abonos primero.');
                        return $this->redirectToRoute('despachoorden_edit', ['id' => $despachoOrden->getId()]);
                    }
                    // Validación: no se puede cambiar a "Pagado" si hay saldo pendiente
                    elseif ($newPaymentStatus == 2 && $despachoOrden->getSaldoPendiente() > 0.01) {
                        $saldoPendiente = $despachoOrden->getSaldoPendiente();
                        $this->addFlash('error', "No se puede marcar como pagado. Saldo pendiente: $" . number_format($saldoPendiente, 2));
                        return $this->redirectToRoute('despachoorden_edit', ['id' => $despachoOrden->getId()]);
                    }
                }

                $items_str = "";
                if (array_key_exists("statusOrden", $request->get('appbundle_despachoorden'))) {
                    if ($previousStat == 1 && $request->get('appbundle_despachoorden')["statusOrden"] == 2) {
                        $this->addFlash(
                            'error',
                            'Para establecer la orden como despachada debes hacerlo desde el botón confirmar despacho, en la vista general de la orden.'
                        );
                        return $this->redirectToRoute('despachoorden_index');
                    }
                }
                
                $em->flush();
                
                foreach ($request->request as $key => $value) {
                    if (strpos($key, "producto") !== false) {
                        $counter = explode("-", $key)[2];
                        foreach ($items as $item) {
                            if (!in_array($item->getId(), $array_product)) {
                                $em->remove($item);
                                $em->flush();
                            }
                        }
                        if (count(explode("-", $key)) == 3) {
                            $item_id = explode("-", $key)[1];
                            $item = $this->getDoctrine()->getRepository(DespachoOrdenItem::class)->find(explode("-", $key)[1]);
                            $item->setCantidad($request->request->get('producto-' . $item_id . '-' . $counter));
                            $item->setColor($request->request->get('color-' . $item_id . '-' . $counter));
                            $item->setPrecio($request->request->get('precio-' . $item_id . '-' . $counter));
                            $item->setBodega($request->request->get('appbundle_despachoorden')['clienteTipo']);
                            
                            if (array_key_exists("statusOrden", $request->get('appbundle_despachoorden'))) {
                                if ($item->getEstatus() == 2 && $request->get('appbundle_despachoorden')["statusOrden"] == 1) {
                                    $prod_color = $em->getRepository('AppBundle:ProductoColor')->findOneBy(['producto' => $item->getProducto()->getProducto()->getId(), 'nombre' => $request->request->get('color-' . $item_id . '-' . $counter)]);
                                    $prod_inventario = $em->getRepository('AppBundle:ProductoInventario')->findOneBy(['producto' => $item->getProducto(), 'color' => $prod_color]);
                                    
                                    if ($item->getBodega() == "MAYORISTA" && $prod_inventario) {
                                        $prod_inventario->setQtyActualMayorista($prod_inventario->getQtyActualMayorista() + $item->getCantidad());
                                        $prod_inventario->setIngresoMayorista($prod_inventario->getIngresoMayorista() + $item->getCantidad());
                                        $em->persist($prod_inventario);
                                        $em->flush();
                                        
                                        // Guardar movimiento
                                        $inventarioMovimiento = new ProductoInventarioMovimiento();
                                        $inventarioMovimiento->setProducto($item->getProducto()->getProducto()->getNombre() . " Talla: " . $item->getProducto()->getNombre());
                                        $inventarioMovimiento->setColor($item->getColor());
                                        $inventarioMovimiento->setMovimiento("Ingreso");
                                        $inventarioMovimiento->setCantidad($item->getCantidad());
                                        $inventarioMovimiento->setBodega("mayorista");
                                        $inventarioMovimiento->setInformacion("Ingreso por cambio manual de Despachado a Por Despachar en orden VRX-" . $despachoOrden->getId());
                                        $inventarioMovimiento->setUsuario($user->getName());
                                        $inventarioMovimiento->setFecha($fecha);
                                        $em->persist($inventarioMovimiento);
                                        $em->flush();
                                    } elseif ($item->getBodega() == "DETAL" && $prod_inventario) {
                                        $prod_inventario->setQtyActualDetal($prod_inventario->getQtyActualDetal() + $item->getCantidad());
                                        $prod_inventario->setIngresoDetal($prod_inventario->getIngresoDetal() + $item->getCantidad());
                                        $em->persist($prod_inventario);
                                        $em->flush();
                                        
                                        // Guardar movimiento
                                        $inventarioMovimiento = new ProductoInventarioMovimiento();
                                        $inventarioMovimiento->setProducto($item->getProducto()->getProducto()->getNombre() . " Talla: " . $item->getProducto()->getNombre());
                                        $inventarioMovimiento->setColor($item->getColor());
                                        $inventarioMovimiento->setMovimiento("Ingreso");
                                        $inventarioMovimiento->setCantidad($item->getCantidad());
                                        $inventarioMovimiento->setBodega("detal");
                                        $inventarioMovimiento->setInformacion("Ingreso por cambio manual de Despachado a Por Despachar en orden VRX-" . $despachoOrden->getId());
                                        $inventarioMovimiento->setUsuario($user->getName());
                                        $inventarioMovimiento->setFecha($fecha);
                                        $em->persist($inventarioMovimiento);
                                        $em->flush();
                                    }
                                }
                                $item->setEstatus($request->get('appbundle_despachoorden')["statusOrden"]);
                            }
                            
                            $em->persist($item);
                            $em->flush();
                            
                            $items_str = $items_str . '<tr>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $item->getProducto()->getProducto()->getNombre() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $item->getProducto()->getProducto()->getReferencia() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $item->getProducto()->getNombre() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $item->getColor() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $item->getCantidad() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $item->getPrecio() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . ($item->getPrecio() * (int)$item->getCantidad()) . '</td>
                            </tr>';
                        } else {
                            $product_id = explode("-", $key)[1];
                            $producto = $this->getDoctrine()->getRepository(ProductoTalla::class)->find($product_id);
                            $color = $em->getRepository('AppBundle:ProductoColor')->findOneBy(array('producto' => $producto->getProducto(), 'nombre' => $request->request->get('color-' . $product_id . '-' . $counter . '-new')));
                            $existencia = $em->getRepository('AppBundle:ProductoInventario')->findOneBy(array('producto' => $producto, 'color' => $color));
                            
                            if ($despachoOrden->getClienteTipo() == "MAYORISTA" && $existencia->getQtyActualMayorista() < $request->request->get('producto-' . $product_id . '-' . $counter . '-new')) {
                                $this->addFlash(
                                    'error',
                                    'El producto ' . $producto->getProducto()->getNombre() . ' Talla: ' . $producto->getNombre() . ' No tiene la disponibilidad suficiente en el inventario'
                                );
                                return $this->redirectToRoute('despachoorden_index');
                            } elseif ($despachoOrden->getClienteTipo() == "DETAL" && $existencia->getQtyActualDetal() < $request->request->get('producto-' . $product_id . '-' . $counter . '-new')) {
                                $this->addFlash(
                                    'error',
                                    'El producto ' . $producto->getProducto()->getNombre() . ' Talla: ' . $producto->getNombre() . ' No tiene la disponibilidad suficiente en el inventario'
                                );
                                return $this->redirectToRoute('despachoorden_index');
                            }
                            
                            $orden_item = new DespachoOrdenItem();
                            $orden_item->setProducto($producto);
                            $orden_item->setColor($request->request->get('color-' . $product_id . '-' . $counter . '-new'));
                            $orden_item->setOrdenDespacho($despachoOrden);
                            $orden_item->setCantidad($request->request->get('producto-' . $product_id . '-' . $counter . '-new'));
                            $orden_item->setPrecio($request->request->get('precio-' . $product_id . '-' . $counter . '-new'));
                            $orden_item->setBodega($request->request->get('appbundle_despachoorden')['clienteTipo']);
                            $orden_item->setEstatus(1);
                            $em->persist($orden_item);
                            $em->flush();
                            
                            $items_str = $items_str . '<tr>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $orden_item->getProducto()->getProducto()->getNombre() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $orden_item->getProducto()->getProducto()->getReferencia() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $orden_item->getProducto()->getNombre() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $orden_item->getColor() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $orden_item->getCantidad() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . $orden_item->getPrecio() . '</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">' . ($orden_item->getPrecio() * (int)$orden_item->getCantidad()) . '</td>
                            </tr>';
                        }
                    }
                }
                
                // Enviar correo al admin - ACTUALIZADO para incluir información de abonos
                try {
                    $url = "https://www.veroxcloset.com/send-email";
                    $client2 = new Client();
                    
                    // Información adicional de abonos para el email
                    $infoAbonos = '';
                    if ($despachoOrden->tieneAbonos()) {
                        $infoAbonos = '<h3>Información de Abonos:</h3>';
                        if ($despachoOrden->getAbono1() !== null) {
                            $infoAbonos .= '<p><strong>Abono 1:</strong> ' . number_format($despachoOrden->getAbono1(), 2) . ' (Fecha: ' . $despachoOrden->getFechaAbono1()->format('Y-m-d H:i:s') . ')</p>';
                        }
                        if ($despachoOrden->getAbono2() !== null) {
                            $infoAbonos .= '<p><strong>Abono 2:</strong> ' . number_format($despachoOrden->getAbono2(), 2) . ' (Fecha: ' . $despachoOrden->getFechaAbono2()->format('Y-m-d H:i:s') . ')</p>';
                        }
                        $infoAbonos .= '<p><strong>Total Abonos:</strong> ' . number_format($despachoOrden->getTotalAbonos(), 2) . '</p>';
                        $infoAbonos .= '<p><strong>Saldo Pendiente:</strong> ' . number_format($despachoOrden->getSaldoPendiente(), 2) . '</p>';
                    }
                    
                    $options = [
                        'form_params' => [
                            "json_string" => json_encode([
                                "email_to" => "disenograficovrx@gmail.com",
                                "email_cc" => "veroxcloset@veroxcloset.com",
                                "email_bcc" => "",
                                "email_subject" => "MODIFICACION DE ORDEN DE DESPACHO VRX-" . $despachoOrden->getId(),
                                "email_body" => '<h1>Se ha modificado la orden de despacho!</h1>
                                <span>A continuación los nuevos datos de la orden:</span><br><br>
                                <table style="text-align: center;border:1px solid black">
                                    <tr>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Item</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Referencia</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Talla</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Color</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Cantidad</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Precio</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Subtotal</th>
                                    </tr>' . $items_str . '
                                </table>
                                <br>
                                <h3>Usuario: ' . $despachoOrden->getClienteId()->getNombre() . ' ' . $despachoOrden->getClienteId()->getApellidos() . '</h3>
                                <h3>Modificado por: ' . $user->getName() . '</h3>
                                <h3>Dirección de envío: ' . $despachoOrden->getDireccionEnvio() . '</h3>
                                <h3>Costo Envío: ' . $despachoOrden->getCostoEnvio() . '</h3>
                                <h3>Total: ' . $despachoOrden->getTotal() . '</h3>
                                <h3>Estado de Pago: ' . $despachoOrden->getEstadoPagoTexto() . '</h3>
                                ' . $infoAbonos . '
                                <h3>Medio de Pago: ' . $despachoOrden->getTipoPago() . '</h3>
                                <h3>Referencia del pedido: VRX-' . $despachoOrden->getId() . '</h3>
                                <h3>Anotaciones: ' . $despachoOrden->getNotas() . '</h3><br>
                                <p>Revisa en la plataforma de administración toda la información del pedido.</p><br>
                                <h3>Atentamente,</h3>
                                <h4>VEROXCLOSET</h4>'
                            ])
                        ]
                    ];
                    $client2->post($url, $options);
                } catch (\Throwable $th) {
                    // Silenciar errores de email
                }
                
                $this->addFlash(
                    'success',
                    'Registro editado correctamente'
                );
                return $this->redirectToRoute('despachoorden_index');
            }
            
            // Calcular información de abonos para la vista
            $infoAbonos = [
                'total_abonos' => $despachoOrden->getTotalAbonos(),
                'saldo_pendiente' => $despachoOrden->getSaldoPendiente(),
                'puede_abonar' => $despachoOrden->getStatusPago() != 2 && $despachoOrden->getSaldoPendiente() > 0.01,
                'abonos_completos' => $despachoOrden->getAbono1() !== null && $despachoOrden->getAbono2() !== null
            ];
            
            return $this->render('despachoorden/edit.html.twig', array(
                'despachoOrden' => $despachoOrden,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                "items" => $items,
                "colors" => $colors,
                "info_abonos" => $infoAbonos
            ));
        } catch (\Throwable $th) {
            dump($th);
            exit;
        }
    }

    /**
     * Deletes a despachoOrden entity.
     *
     */
    public function deleteAction(Request $request, DespachoOrden $despachoOrden)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $form = $this->createDeleteForm($despachoOrden);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            if ($despachoOrden->getAnulada()) {
                $this->addFlash('error', 'Esta orden ya fue anulada anteriormente');
                return $this->redirectToRoute('despachoorden_index');
            }
            
            $now = new \DateTime();
            $ordenInfo = [
                'id' => $despachoOrden->getId(),
                'usuario_anulacion' => $user->getName(),
                'fecha_anulacion' => $now->format('Y-m-d H:i:s'),
                'cliente' => $despachoOrden->getClienteId()->getNombre() . ' ' . $despachoOrden->getClienteId()->getApellidos(),
                'items_liberados' => 0
            ];

            $this->get('monolog.logger.orden_anulada')->info("=== INICIO ANULACIÓN ORDEN ===", $ordenInfo);
            
            $despachoOrden->setAnulada(1);
            $despachoOrden->setFechaAnulacion($now);
            $despachoOrden->setUsuarioCreacion($user);

            $items = $em->getRepository('AppBundle:DespachoOrdenItem')->findBy([
                'ordenDespacho' => $despachoOrden->getId(),
                'estatus' => 1 
            ]);
            
            $itemsLiberados = 0;
            foreach ($items as $item) {
                $productoTalla = $item->getProducto();
                $colorNombre = $item->getColor();
                $bodega = $item->getBodega();
                $cantidad = $item->getCantidad();
                
                $productoPadre = $productoTalla->getProducto();
                $color = $em->getRepository('AppBundle:ProductoColor')->findOneBy([
                    'producto' => $productoPadre,
                    'nombre' => $colorNombre
                ]);
                
                if ($color) {
                    $inventario = $em->getRepository('AppBundle:ProductoInventario')->findOneBy([
                        'producto' => $productoTalla,
                        'color' => $color
                    ]);
                    
                    if ($inventario) {
                        $cantidadAnterior = ($bodega == 'MAYORISTA') ? 
                            $inventario->getQtyActualMayorista() : 
                            $inventario->getQtyActualDetal();
                        
                        if ($bodega == 'MAYORISTA') {
                            $inventario->setQtyActualMayorista($inventario->getQtyActualMayorista() + $cantidad);
                        } else {
                            $inventario->setQtyActualDetal($inventario->getQtyActualDetal() + $cantidad);
                        }
                        
                        $cantidadNueva = ($bodega == 'MAYORISTA') ? 
                            $inventario->getQtyActualMayorista() : 
                            $inventario->getQtyActualDetal();
                        
                        $em->persist($inventario);

                        $this->get('monolog.logger.orden_anulada')->info("Inventario liberado", [
                            'orden_id' => $despachoOrden->getId(),
                            'producto' => $productoPadre->getNombre() . " Talla: " . $productoTalla->getNombre(),
                            'color' => $colorNombre,
                            'bodega' => $bodega,
                            'cantidad_liberada' => $cantidad,
                            'stock_anterior' => $cantidadAnterior,
                            'stock_nuevo' => $cantidadNueva
                        ]);

                        $movimiento = new ProductoInventarioMovimiento();
                        $movimiento->setProducto($productoPadre->getNombre() . " Talla: " . $productoTalla->getNombre());
                        $movimiento->setColor($colorNombre);
                        $movimiento->setMovimiento("Liberación reserva");
                        $movimiento->setCantidad($cantidad);
                        $movimiento->setBodega(strtolower($bodega));
                        $movimiento->setInformacion("Anulación orden VRX-" . $despachoOrden->getId());
                        $movimiento->setUsuario($user->getName());
                        $movimiento->setFecha($now);
                        $em->persist($movimiento);
                        
                        $itemsLiberados++;
                    }
                }
                
                $item->setEstatus(0);
                $em->persist($item);
            }

            $despachados = $em->getRepository('AppBundle:DespachoOrdenItem')->findBy([
                'ordenDespacho' => $despachoOrden->getId(),
                'estatus' => 2
            ]);
            
            foreach ($despachados as $item) {
                $item->setEstatus(0);
                $em->persist($item);
            }
            
            $ordenInfo['items_liberados'] = $itemsLiberados;
            $ordenInfo['items_despachados_anulados'] = count($despachados);
            
            try {
                $em->flush();
                $this->get('monolog.logger.orden_anulada')->info("Orden anulada exitosamente", $ordenInfo);
                
            } catch (\Exception $e) {
                $this->get('monolog.logger.orden_anulada')->error("Error al anular orden", [
                    'orden_id' => $despachoOrden->getId(),
                    'error' => $e->getMessage(),
                    'usuario' => $user->getName()
                ]);
                
                $this->addFlash('error', 'Error al anular: ' . $e->getMessage());
                return $this->redirectToRoute('despachoorden_index');
            }
            try {
                $items_str = "";
                $orden_items = $em->getRepository('AppBundle:DespachoOrdenItem')->findBy([
                    'ordenDespacho' => $despachoOrden->getId()
                ]);
                
                foreach ($orden_items as $item) {
                    $producto = $item->getProducto();
                    $productoPadre = $producto ? $producto->getProducto() : null;
                    
                    $items_str .= '<tr>
                        <td style="text-align: center;border:1px solid black;padding:10px;">'.($productoPadre ? $productoPadre->getNombre() : 'N/A').'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">'.($productoPadre ? $productoPadre->getReferencia() : 'N/A').'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">'.($producto ? $producto->getNombre() : 'N/A').'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getColor().'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getCantidad().'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">$'.$item->getPrecio().'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">$'.($item->getPrecio()*(int)$item->getCantidad()).'</td>
                    </tr>';
                }

                $url = "https://www.veroxcloset.com/send-email";
                $client3 = new Client();
                $options = [
                    'form_params' => [
                        "json_string" => json_encode([
                            "email_to"=>"disenograficovrx@gmail.com",
                            "email_cc"=>"veroxcloset@veroxcloset.com",
                            "email_bcc" => "",
                            "email_subject" => "Orden de despacho anulada: VRX-".$despachoOrden->getId(),
                            "email_body" => '<h1>Se ha anulado la orden de despacho!</h1>
                                <span>A continuación los datos de la orden:</span><br><br>
                                <span>Anulada por: '.$user->getName().'</span><br>
                                <span>Fecha: '.$now->format('Y-m-d H:i:s').'</span><br>
                                <span>Items liberados: '.$itemsLiberados.'</span><br><br>
                                <table style="text-align: center;border:1px solid black">
                                    <tr>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Item</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Referencia</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Talla</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Color</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Cantidad</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Precio</th>
                                        <th style="text-align: center;border:1px solid black;padding:10px;">Subtotal</th>
                                    </tr>'.$items_str.'
                                </table>
                                <span>Cliente: '.$despachoOrden->getClienteId()->getNombre().' '.$despachoOrden->getClienteId()->getApellidos().'</span><br>
                                <span>Email: '.$despachoOrden->getClienteId()->getEmail().'</span><br>
                                <h3>Atentamente,</h3>
                                <h4>Equipo VEROXCLOSET<h4>'
                        ])
                    ]
                ];
                $client3->post($url, $options);
                
                $this->get('monolog.logger.orden_anulada')->info("Email de anulación enviado", [
                    'orden_id' => $despachoOrden->getId(),
                    'usuario' => $user->getName()
                ]);
                
            } catch (\Throwable $th) {
                $this->get('monolog.logger.orden_anulada')->error("Error enviando email de anulación", [
                    'orden_id' => $despachoOrden->getId(),
                    'error' => $th->getMessage(),
                    'usuario' => $user->getName()
                ]);
            }
            
            $this->get('monolog.logger.orden_anulada')->info("=== FIN ANULACIÓN ORDEN ===", [
                'orden_id' => $despachoOrden->getId(),
                'resultado' => 'exitoso',
                'items_liberados' => $itemsLiberados
            ]);
            
            $this->addFlash('success', "Orden anulada correctamente. Items liberados: $itemsLiberados");
        }

        return $this->redirectToRoute('despachoorden_index');
    }

    /**
     * Creates a form to delete a despachoOrden entity.
     *
     * @param DespachoOrden $despachoOrden The despachoOrden entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DespachoOrden $despachoOrden)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('despachoorden_delete', array('id' => $despachoOrden->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(DespachoOrden $despachoOrden)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($despachoOrden);
        $em->flush($despachoOrden);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('despachoorden_index');
    }

    public function pagadoAction(DespachoOrden $despachoOrden)
    {
        $em = $this->getDoctrine()->getManager();
        
        if ($despachoOrden->tieneAbonos() && $despachoOrden->getSaldoPendiente() > 0.01) {
            $this->addFlash('error', 'Esta orden tiene saldo pendiente. Complete los pagos primero.');
            return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
        }

        $despachoOrden->setStatusPago(2);
        if (!$despachoOrden->getFechaPago()) {
            $despachoOrden->setFechaPago(new \DateTime());
        }
        $em->persist($despachoOrden);
        $em->flush($despachoOrden);
        
        $deleteForm = $this->createDeleteForm($despachoOrden);
        $items = $em->getRepository('AppBundle:DespachoOrdenItem')
            ->createQueryBuilder('a')
            ->where('a.ordenDespacho = :orden')
            ->setParameter('orden', $despachoOrden)
            ->getQuery()
            ->getResult();

        // NUEVO: Calcular información de abonos para la vista
        $infoAbonos = [
            'total_abonos' => $despachoOrden->getTotalAbonos(),
            'saldo_pendiente' => $despachoOrden->getSaldoPendiente(),
            'puede_abonar' => false, // Ya está pagado
            'abonos_completos' => $despachoOrden->getAbono1() !== null && $despachoOrden->getAbono2() !== null,
            'puede_completar_pago' => false // Ya está pagado
        ];

        $this->addFlash('success', 'Pago confirmado correctamente');
        
        return $this->render('despachoorden/show.html.twig', array(
            'despachoOrden' => $despachoOrden,
            'delete_form' => $deleteForm->createView(),
            'items' => $items,
            'info_abonos' => $infoAbonos,
            'user' => strtoupper($this->container->get('security.token_storage')->getToken()->getUser()->getName())
        ));
    }

    public function despachadoAction(DespachoOrden $despachoOrden,Request $request){
        $fecha = new \DateTime();
        $em = $this->getDoctrine()->getManager();
        $despachoOrden->setStatusOrden(2);
        $despachoOrden->setFechaDespacho($fecha);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $despachoOrden->setNotas($despachoOrden->getNotas()." // Guía de Transporte: ".$request->query->get('guia'));
        $em->persist($despachoOrden);
        $em->flush($despachoOrden);
        $deleteForm = $this->createDeleteForm($despachoOrden);
        $items = $em->getRepository('AppBundle:DespachoOrdenItem')
              ->createQueryBuilder('a')
              ->where('a.ordenDespacho = :orden')
              ->setParameter('orden', $despachoOrden)
              ->getQuery()
              ->getResult();
        foreach ($items as $item) {
            $color_id = $this->getDoctrine()->getRepository(ProductoColor::class)->findOneBy(array('nombre' => $item->getColor(),'producto' => $item->getProducto()->getProducto()));
            $inventario_item = $this->getDoctrine()->getRepository(ProductoInventario::class)->findOneBy(array('producto' => $item->getProducto(),'color' => $color_id));
            if($despachoOrden->getClienteTipo() == "MAYORISTA"){
                $inventario_item->setQtyActualMayorista(($inventario_item->getQtyActualMayorista() - $item->getCantidad() < 0)?0:$inventario_item->getQtyActualMayorista() - $item->getCantidad());
                $inventario_item->setEgresoMayorista($inventario_item->getEgresoMayorista() + $item->getCantidad());
                $inventario_item->setUltimoEgresoM($fecha);
                $em->persist($inventario_item);
                $em->flush();
                if($inventario_item->getQtyActualMayorista()<=0){
                    //Enviar notificacion de producto agotado
                    try {
                        $url = "https://www.veroxcloset.com/send-email";
                        $client3 = new Client();
                        $options = [
                            'form_params' => [
                                "json_string" => json_encode([
                                    "email_to"=>"disenograficovrx@gmail.com",
                                    "email_cc"=>"veroxcloset@veroxcloset.com",
                                    "email_bcc"=>"",
                                    "email_subject"=>"Producto agotado en inventario Mayorista: ".$inventario_item->getProducto()->getProducto()->getNombre(),
                                    "email_body"=>'<h1>Se ha agotado el producto en el inventario Mayorista!</h1>
                                    <span>A continuación los datos del producto:</span><br><br>
                                    <h2>'.$inventario_item->getProducto()->getProducto()->getNombre().' Talla:'.$inventario_item->getProducto()->getNombre().' Color:'.$inventario_item->getColor()->getNombre().'</h2>
                                    <br>
                                    <p>Debes hacer ingreso de este producto al inventario de mayoristas.</p><br>
                                    <h3>Atentamente,</h3>
                                    <h4>Equipo VEROXCLOSET<h4>'
                                ])
                            ]
                        ];
                        $client3->post($url,$options);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                    //Pasar unidades de detal a Cero
                    $inventario_item->setQtyActualDetal(0);
                    $em->persist($inventario_item);
                    $em->flush();
                }
            }
            else{
                $inventario_item->setQtyActualDetal(($inventario_item->getQtyActualDetal() - $item->getCantidad() < 0)?0:$inventario_item->getQtyActualDetal() - $item->getCantidad());
                $inventario_item->setEgresoDetal($inventario_item->getEgresoDetal() + $item->getCantidad());
                $inventario_item->setUltimoEgresoD($fecha);
                $em->persist($inventario_item);
            }
            $item->setEstatus(2);
            $em->flush();
            //guardar movimiento
            $inventarioMovimiento = new ProductoInventarioMovimiento();
            $inventarioMovimiento->setProducto($item->getProducto()->getProducto()->getNombre()." Talla: ".$item->getProducto()->getNombre());
            $inventarioMovimiento->setColor($color_id->getNombre());
            $inventarioMovimiento->setMovimiento("Egreso");
            $inventarioMovimiento->setCantidad($item->getCantidad());
            $inventarioMovimiento->setBodega(($despachoOrden->getClienteTipo() == "MAYORISTA")?"mayorista":"detal");
            $inventarioMovimiento->setInformacion("Egreso por despacho de orden VRX-".$despachoOrden->getId());
            $inventarioMovimiento->setUsuario($user->getName());
            $inventarioMovimiento->setFecha($fecha);
            $em->persist($inventarioMovimiento);
            $em->flush();
        }

        // Enviar mail al cliente
        try {
            $url = "https://www.veroxcloset.com/send-email";//CAMBIAR EN PRODUCCION
            $client2 = new Client();
            $options = [
                'form_params' => [
                    "json_string" => json_encode([
                        "email_to"=>$despachoOrden->getClienteId()->getEmail(),
                        "email_cc"=>"veroxcloset@veroxcloset.com",
                        "email_bcc"=>"disenograficovrx@gmail.com",
                        "email_subject"=>"Confirmación de despacho pedido VRX-".$despachoOrden->getId(),
                        "email_body"=>'<h1>Hemos enviado tu pedido!</h1>
                        <span>A continuación los datos del despacho:</span><br><br>
                        <h2>Guía de Transporte: '.$request->query->get('guia').'</h2>
                        <br>
                        <h3>Puedes rastrear el paquete en el siguientelink</h3>
                        <a href="https://www.dhl.com/us-en/home.html">DHL tracking</a>
                        <p>Te estaremos enviando más información en caso de ser necesario.</p><br>
                        <h3>Atentamente,</h3>
                        <h4>Equipo VEROXCLOSET<h4>'
                    ])
                ]
            ];
            $client2->post($url,$options);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $infoAbonos = [
            'total_abonos' => $despachoOrden->getTotalAbonos(),
            'saldo_pendiente' => $despachoOrden->getSaldoPendiente(),
            'puede_abonar' => $despachoOrden->getStatusPago() != 2 && $despachoOrden->getSaldoPendiente() > 0.01,
            'abonos_completos' => $despachoOrden->getAbono1() !== null && $despachoOrden->getAbono2() !== null,
            'puede_completar_pago' => $despachoOrden->getStatusPago() == 3 && $despachoOrden->getSaldoPendiente() > 0.01
        ];

        $this->addFlash(
            'success',
            'Registro editado correctamente'
        );
        return $this->render('despachoorden/show.html.twig', array(
            'despachoOrden' => $despachoOrden,
            'delete_form' => $deleteForm->createView(),
            'info_abonos' => $infoAbonos,
            'user' => strtoupper($user->getName()),
            'items'=>$items
        ));
    }

    /**
     * @Route("/cierre-mensual", name="despacho_orden_cierre_mensual")
     */
    public function cierreMensualAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $startDate = new \DateTime('first day of last month');
        $endDate = new \DateTime('last day of last month');
        $endDate->setTime(23, 59, 59);

        $mesAnterior = $startDate->format('F Y');
        $mesAnteriorEsp = $this->traducirMes($startDate->format('n'), $startDate->format('Y'));

        $ordenes = $em->getRepository('AppBundle:DespachoOrden')->createQueryBuilder('o')
            ->where('o.statusPago = :statusPago')
            ->andWhere('o.anulada = :anulada')
            ->andWhere('o.fechaCreacion BETWEEN :start AND :end')
            ->setParameter('statusPago', 1)
            ->setParameter('anulada', 0)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getResult();
        
        $contador = 0;
        $now = new \DateTime();
        $ordenesAnuladas = [];

        $this->get('monolog.logger.cierre_mensual')->info("=== INICIO CIERRE MENSUAL ===", [
            'mes_procesado' => $mesAnteriorEsp,
            'fecha_cierre' => $now->format('Y-m-d H:i:s'),
            'total_ordenes_encontradas' => count($ordenes),
            'rango_fechas' => $startDate->format('Y-m-d') . ' al ' . $endDate->format('Y-m-d')
        ]);
        
        foreach ($ordenes as $orden) {
            $ordenInfo = [
                'id' => $orden->getId(),
                'numero_orden' => $orden->getId(),
                'cliente' => $orden->getClienteId(),
                'fecha_creacion' => $orden->getFechaCreacion()->format('Y-m-d H:i:s'),
                'total' => $orden->getTotal(),
                'items_liberados' => 0
            ];

            $orden->setAnulada(1);
            $orden->setFechaAnulacion($now);

            $notas = $orden->getNotas() . "\n\nANULADA POR CIERRE MENSUAL - " . $now->format('Y-m-d H:i') . 
                    " (Orden sin pagos - Mes: $mesAnteriorEsp)";
            $orden->setNotas($notas);

            $items = $em->getRepository('AppBundle:DespachoOrdenItem')->findBy(['ordenDespacho' => $orden->getId()]);
            $itemsLiberados = 0;

            foreach ($items as $item) {
                if ($item->getEstatus() == 1) {
                    $prodColor = $em->getRepository('AppBundle:ProductoColor')
                        ->findOneBy(['producto' => $item->getProducto()->getProducto()->getId(), 'nombre' => $item->getColor()]);
                    
                    if ($prodColor) {
                        $inventario = $em->getRepository('AppBundle:ProductoInventario')
                            ->findOneBy(['producto' => $item->getProducto(), 'color' => $prodColor]);
                        
                        if ($inventario) {
                            $cantidadAnterior = $item->getBodega() == 'MAYORISTA' ? 
                                $inventario->getQtyActualMayorista() : 
                                $inventario->getQtyActualDetal();
                                
                            if ($item->getBodega() == 'MAYORISTA') {
                                $inventario->setQtyActualMayorista($inventario->getQtyActualMayorista() + $item->getCantidad());
                            } else {
                                $inventario->setQtyActualDetal($inventario->getQtyActualDetal() + $item->getCantidad());
                            }
                            
                            $cantidadNueva = $item->getBodega() == 'MAYORISTA' ? 
                                $inventario->getQtyActualMayorista() : 
                                $inventario->getQtyActualDetal();

                            $this->get('monolog.logger.cierre_mensual')->info("Inventario liberado", [
                                'orden_id' => $orden->getId(),
                                'producto' => $item->getProducto()->getNombre() ?? 'N/A',
                                'color' => $item->getColor(),
                                'bodega' => $item->getBodega(),
                                'cantidad_liberada' => $item->getCantidad(),
                                'stock_anterior' => $cantidadAnterior,
                                'stock_nuevo' => $cantidadNueva
                            ]);
                            
                            $em->persist($inventario);
                            $itemsLiberados++;
                        }
                    }
                }
            }
            
            $ordenInfo['items_liberados'] = $itemsLiberados;
            $ordenesAnuladas[] = $ordenInfo;

            $this->get('monolog.logger.cierre_mensual')->info("Orden anulada", $ordenInfo);
            
            $em->persist($orden);
            $contador++;
        }
        
        $em->flush();

        $this->get('logger')->info("=== FIN CIERRE MENSUAL ===", [
            'mes_procesado' => $mesAnteriorEsp,
            'fecha_cierre' => $now->format('Y-m-d H:i:s'),
            'total_ordenes_anuladas' => $contador,
            'ordenes_anuladas' => array_column($ordenesAnuladas, 'id')
        ]);

        $mensaje = "Cierre mensual de $mesAnteriorEsp realizado exitosamente: $contador órdenes anuladas";
        $this->addFlash('success', $mensaje);

        if ($contador > 0) {
            $this->get('monolog.logger.cierre_mensual')->notice("CIERRE MENSUAL EJECUTADO", [
                'mes' => $mesAnteriorEsp,
                'ordenes_anuladas' => $contador,
                'usuario' => $this->getUser() ? $this->getUser()->getUsername() : 'Sistema',
                'ip' => $this->get('request_stack')->getCurrentRequest()->getClientIp()
            ]);
        }
        
        return $this->redirectToRoute('despachoorden_index');
    }

    /**
     * Traduce el número del mes al español
     */
    private function traducirMes($numeroMes, $year)
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero', 
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        
        return $meses[$numeroMes] . ' ' . $year;
    }

    /**
     * Registra un abono para una orden
     * @Route("/{id}/registrar-abono", name="despachoorden_registrar_abono", methods={"POST"})
     */
    public function registrarAbonoAction(Request $request, DespachoOrden $despachoOrden)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $monto = floatval($request->request->get('monto_abono'));
            $fecha = new \DateTime();
            $user = $this->container->get('security.token_storage')->getToken()->getUser();

            // Validaciones
            if ($monto <= 0) {
                $this->addFlash('error', 'El monto del abono debe ser mayor a cero');
                return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
            }

            if ($despachoOrden->getStatusPago() == 2) {
                $this->addFlash('error', 'Esta orden ya está completamente pagada');
                return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
            }

            // CORREGIDO: Lógica directa para registrar abonos
            $saldoPendiente = $despachoOrden->getSaldoPendiente();
            
            if ($monto >= $despachoOrden->getTotal()) {
                $this->addFlash('error', 'El abono no puede ser mayor o igual al total de la orden');
                return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
            }
            
            if ($monto > $saldoPendiente) {
                $this->addFlash('error', 'El abono excede el saldo pendiente');
                return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
            }

            // Registrar el abono en el campo correspondiente
            if ($despachoOrden->getAbono1() === null) {
                $despachoOrden->setAbono1($monto);
                $despachoOrden->setFechaAbono1($fecha);
                $numeroAbono = 1;
            } elseif ($despachoOrden->getAbono2() === null) {
                $despachoOrden->setAbono2($monto);
                $despachoOrden->setFechaAbono2($fecha);
                $numeroAbono = 2;
            } else {
                $this->addFlash('error', 'No se pueden registrar más de 2 abonos para esta orden');
                return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
            }

            // Actualizar estado a "Abonado" si no está pagado completamente
            if ($despachoOrden->getStatusPago() != 2) {
                $despachoOrden->setStatusPago(3); // Estado abonado
            }

            $em->persist($despachoOrden);
            $em->flush();

            // Enviar email de notificación de abono
            $this->enviarEmailAbono($despachoOrden, $monto, $user);

            $this->addFlash('success', "Abono #{$numeroAbono} registrado correctamente por $" . number_format($monto, 2));
            
            return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
            
        } catch (\Throwable $th) {
            $this->addFlash('error', 'Error al registrar el abono: ' . $th->getMessage());
            return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
        }
    }

    /**
     * Completa el pago de una orden con abonos
     * @Route("/{id}/completar-pago", name="despachoorden_completar_pago", methods={"POST"})
     */
    public function completarPagoAction(Request $request, DespachoOrden $despachoOrden)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $montoPagoFinal = floatval($request->request->get('monto_pago_final'));
            $fecha = new \DateTime();
            $user = $this->container->get('security.token_storage')->getToken()->getUser();

            // Validaciones
            if ($despachoOrden->getStatusPago() == 2) {
                $this->addFlash('error', 'Esta orden ya está completamente pagada');
                return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
            }

            $saldoPendiente = $despachoOrden->getSaldoPendiente();
            
            if (abs($montoPagoFinal - $saldoPendiente) > 0.01) {
                $this->addFlash('error', "El monto debe ser exactamente $" . number_format($saldoPendiente, 2) . " (saldo pendiente)");
                return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
            }

            // CORREGIDO: Lógica directa para completar el pago sin función externa
            $despachoOrden->setStatusPago(2); // Marcar como pagado
            $despachoOrden->setFechaPago($fecha); // Establecer fecha de pago
            
            $em->persist($despachoOrden);
            $em->flush();

            // Enviar email de confirmación de pago completo
            $this->enviarEmailPagoCompleto($despachoOrden, $user);

            $this->addFlash('success', 'Pago completado correctamente. Total pagado: $' . number_format($despachoOrden->getTotal(), 2));
            
            return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
            
        } catch (\Throwable $th) {
            $this->addFlash('error', 'Error al completar el pago: ' . $th->getMessage());
            return $this->redirectToRoute('despachoorden_show', ['id' => $despachoOrden->getId()]);
        }
    }

    /**
     * Envía email de notificación de abono
     */
    private function enviarEmailAbono(DespachoOrden $despachoOrden, $monto, $user)
    {
        try {
            $numeroAbono = $despachoOrden->getAbono2() !== null ? 2 : 1;
            $totalAbonos = $despachoOrden->getTotalAbonos();
            $saldoPendiente = $despachoOrden->getSaldoPendiente();
            
            $url = "https://www.veroxcloset.com/send-email";
            $client = new Client();
            $options = [
                'form_params' => [
                    "json_string" => json_encode([
                        "email_to" => "disenograficovrx@gmail.com",
                        "email_cc" => "veroxcloset@veroxcloset.com",
                        "email_bcc" => "",
                        "email_subject" => "ABONO #{$numeroAbono} REGISTRADO - ORDEN VRX-" . $despachoOrden->getId(),
                        "email_body" => '
                            <h1>Se ha registrado un abono!</h1>
                            <h2>Detalles del abono:</h2>
                            <p><strong>Orden:</strong> VRX-' . $despachoOrden->getId() . '</p>
                            <p><strong>Cliente:</strong> ' . $despachoOrden->getClienteId()->getNombre() . ' ' . $despachoOrden->getClienteId()->getApellidos() . '</p>
                            <p><strong>Abono #' . $numeroAbono . ':</strong> $' . number_format($monto, 2) . '</p>
                            <p><strong>Fecha del abono:</strong> ' . date('Y-m-d H:i:s') . '</p>
                            <p><strong>Total factura:</strong> $' . number_format($despachoOrden->getTotal(), 2) . '</p>
                            <p><strong>Total abonos:</strong> $' . number_format($totalAbonos, 2) . '</p>
                            <p><strong>Saldo pendiente:</strong> $' . number_format($saldoPendiente, 2) . '</p>
                            <p><strong>Registrado por:</strong> ' . $user->getName() . '</p>
                            <br>
                            <p>Revisa en la plataforma de administración toda la información del pedido.</p>
                            <br>
                            <h3>Atentamente,</h3>
                            <h4>VEROXCLOSET</h4>'
                    ])
                ]
            ];
            $client->post($url, $options);
        } catch (\Throwable $th) {
            // Silenciar errores de email
        }
    }

    /**
     * Envía email de confirmación de pago completo
     */
    private function enviarEmailPagoCompleto(DespachoOrden $despachoOrden, $user)
    {
        try {
            $url = "https://www.veroxcloset.com/send-email";
            $client = new Client();
            $options = [
                'form_params' => [
                    "json_string" => json_encode([
                        "email_to" => "disenograficovrx@gmail.com",
                        "email_cc" => "veroxcloset@veroxcloset.com",
                        "email_bcc" => "",
                        "email_subject" => "PAGO COMPLETO - ORDEN VRX-" . $despachoOrden->getId(),
                        "email_body" => '
                            <h1>¡Pago completado exitosamente!</h1>
                            <h2>Detalles del pago:</h2>
                            <p><strong>Orden:</strong> VRX-' . $despachoOrden->getId() . '</p>
                            <p><strong>Cliente:</strong> ' . $despachoOrden->getClienteId()->getNombre() . ' ' . $despachoOrden->getClienteId()->getApellidos() . '</p>
                            <p><strong>Total pagado:</strong> $' . number_format($despachoOrden->getTotal(), 2) . '</p>
                            ' . ($despachoOrden->getAbono1() ? '<p><strong>Abono 1:</strong> $' . number_format($despachoOrden->getAbono1(), 2) . ' (Fecha: ' . $despachoOrden->getFechaAbono1()->format('Y-m-d H:i:s') . ')</p>' : '') . '
                            ' . ($despachoOrden->getAbono2() ? '<p><strong>Abono 2:</strong> $' . number_format($despachoOrden->getAbono2(), 2) . ' (Fecha: ' . $despachoOrden->getFechaAbono2()->format('Y-m-d H:i:s') . ')</p>' : '') . '
                            <p><strong>Fecha pago completo:</strong> ' . $despachoOrden->getFechaPago()->format('Y-m-d H:i:s') . '</p>
                            <p><strong>Completado por:</strong> ' . $user->getName() . '</p>
                            <br>
                            <p>La orden está ahora lista para ser procesada y despachada.</p>
                            <br>
                            <h3>Atentamente,</h3>
                            <h4>VEROXCLOSET</h4>'
                    ])
                ]
            ];
            $client->post($url, $options);
        } catch (\Throwable $th) {
            // Silenciar errores de email
        }
    }

}
