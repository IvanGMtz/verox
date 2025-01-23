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
            //$q = null;
            //dump($q);exit;
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
                        if($qcount == 0){
                            $despachoOrdensQ->where('a.'.$field.' = :'.$field)->setParameter($field, (strtoupper($value)=='CONFIRMADO')?2:((strtoupper($value)=='PENDIENTE')?1:0));
                        }else{
                            $despachoOrdensQ->andWhere('a.'.$field.' = :'.$field)->setParameter($field, (strtoupper($value)=='CONFIRMADO')?2:((strtoupper($value)=='PENDIENTE')?1:0));
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
            
            $query = $despachoOrdensQ->orderBy('a.id', 'DESC')
            ->getQuery();
            
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $page, /*page number*/
                50 /*limit per page*/
            );
            
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
            $form = $this->createForm('AppBundle\Form\DespachoOrdenType', $despachoOrden);
            $form->handleRequest($request);
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $fecha = new \DateTime();
            if ($form->isSubmitted() && $form->isValid()) {
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
                'now' => $fecha
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
        return $this->render('despachoorden/show.html.twig', array(
            'despachoOrden' => $despachoOrden,
            'delete_form' => $deleteForm->createView(),
            "items"=>$items,
            'user'=>strtoupper($user->getName())
        ));
    }

    /**
     * Displays a form to edit an existing despachoOrden entity.
     *
     */
    public function editAction(Request $request, DespachoOrden $despachoOrden)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $previousStat = $despachoOrden->getStatusOrden();
            $deleteForm = $this->createDeleteForm($despachoOrden);
            $editForm = $this->createForm('AppBundle\Form\DespachoOrdenType', $despachoOrden);
            $editForm->handleRequest($request);
            $array_product=[];
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
                $colores=  $em->getRepository('AppBundle:ProductoColor')
                ->createQueryBuilder('a')
                ->where('a.producto = :producto')
                ->setParameter('producto', $item->getProducto()->getProducto()->getId())
                ->getQuery()
                ->getResult();
                foreach ($colores as $color) {
                    $existencia = $em->getRepository('AppBundle:ProductoInventario')->findOneBy(['producto' => $item->getProducto(),'color' => $color]);
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
                        if($reservado->getBodega()=="DETAL"){
                            $reserva_detal = $reserva_detal + $reservado->getCantidad();
                        }
                        else{
                            $reserva_mayorista = $reserva_mayorista + $reservado->getCantidad();
                        }
                    }
                    $qtyMayoristas = 0;
                    if($existencia!=null){
                        $qtyMayoristas = ($existencia->getQtyActualMayorista()- $reserva_mayorista<0)?0:$existencia->getQtyActualMayorista() - $reserva_mayorista;
                    }
                    array_push($colors,[
                        "nombre"=>$color->getNombre(),
                        "hex"=>$color->getHex(),
                        "producto_id"=>$item->getProducto()->getId(),
                        "DETAL"=>($existencia!=null)?(($qtyMayoristas <= 0)?0:$existencia->getQtyActualDetal() - $reserva_detal):0,
                        "MAYORISTA"=>($existencia!=null)?$existencia->getQtyActualMayorista()-$reserva_mayorista:0,
                    ]);
                }
            }
            foreach ($request->request as $key=>$param) {
                if(strpos($key,"producto")!== false && count(explode("-",$key))==3){
                    array_push($array_product,explode("-",$key)[1]);
                }
            }
            if ($editForm->isSubmitted() && $editForm->isValid()) {
                //dump($despachoOrden->getStatusOrden(),$request->get('appbundle_despachoorden')["statusOrden"]);exit;
                $items_str = "";
                if(array_key_exists("statusOrden",$request->get('appbundle_despachoorden'))){
                    if($previousStat == 1 && $request->get('appbundle_despachoorden')["statusOrden"] == 2){
                        $this->addFlash(
                            'error',
                            'Para establecer la orden como despachada debes hacerlo desde el botón confirmar despacho, en la vista general de la orden.'
                        );
                        return $this->redirectToRoute('despachoorden_index');
                    }
                }
                $em->flush();
                foreach ($request->request as $key => $value) {
                    if(strpos($key,"producto")!== false){
                        $counter = explode("-",$key)[2];
                        foreach ($items as $item) {
                            if (!in_array($item->getId(),$array_product)) {
                            $em->remove($item);
                            $em->flush();
                            }
                        }
                        if(count(explode("-",$key))==3){
                            $item_id = explode("-",$key)[1];
                            $item = $this->getDoctrine()->getRepository(DespachoOrdenItem::class)->find(explode("-",$key)[1]);
                            $item->setCantidad($request->request->get('producto-'.$item_id.'-'.$counter));
                            $item->setColor($request->request->get('color-'.$item_id.'-'.$counter));
                            $item->setPrecio($request->request->get('precio-'.$item_id.'-'.$counter));
                            $item->setBodega($request->request->get('appbundle_despachoorden')['clienteTipo']);
                            if(array_key_exists("statusOrden",$request->get('appbundle_despachoorden'))){
                                if($item->getEstatus() == 2 && $request->get('appbundle_despachoorden')["statusOrden"] == 1){
                                    $prod_color = $em->getRepository('AppBundle:ProductoColor')->findOneBy(['producto' => $item->getProducto()->getProducto()->getId(),'nombre' => $request->request->get('color-'.$item_id.'-'.$counter)]);
                                    $prod_inventario = $em->getRepository('AppBundle:ProductoInventario')->findOneBy(['producto' => $item->getProducto(),'color' => $prod_color]);
                                    if($item->getBodega()=="MAYORISTA" && $prod_inventario){
                                        $prod_inventario->setQtyActualMayorista($prod_inventario->getQtyActualMayorista() + $item->getCantidad());
                                        $prod_inventario->setIngresoMayorista($prod_inventario->getIngresoMayorista() + $item->getCantidad());
                                        $em->persist($prod_inventario);
                                        $em->flush();
                                        //guardar movimiento
                                        $inventarioMovimiento = new ProductoInventarioMovimiento();
                                        $inventarioMovimiento->setProducto($item->getProducto()->getProducto()->getNombre()." Talla: ".$item->getProducto()->getNombre());
                                        $inventarioMovimiento->setColor($item->getColor());
                                        $inventarioMovimiento->setMovimiento("Ingreso");
                                        $inventarioMovimiento->setCantidad($item->getCantidad());
                                        $inventarioMovimiento->setBodega("mayorista");
                                        $inventarioMovimiento->setInformacion("Ingreso por cambio manual de Despachado a Por Despachar en orden VRX-".$despachoOrden->getId());
                                        $inventarioMovimiento->setUsuario($user->getName());
                                        $inventarioMovimiento->setFecha($fecha);
                                        $em->persist($inventarioMovimiento);
                                        $em->flush();
                                    }
                                    else if($item->getBodega()=="DETAL" && $prod_inventario){
                                        $prod_inventario->setQtyActualDetal($prod_inventario->getQtyActualDetal() + $item->getCantidad());
                                        $prod_inventario->setIngresoDetal($prod_inventario->getIngresoDetal() + $item->getCantidad());
                                        $em->persist($prod_inventario);
                                        $em->flush();
                                        //guardar movimiento
                                        $inventarioMovimiento = new ProductoInventarioMovimiento();
                                        $inventarioMovimiento->setProducto($item->getProducto()->getProducto()->getNombre()." Talla: ".$item->getProducto()->getNombre());
                                        $inventarioMovimiento->setColor($item->getColor());
                                        $inventarioMovimiento->setMovimiento("Ingreso");
                                        $inventarioMovimiento->setCantidad($item->getCantidad());
                                        $inventarioMovimiento->setBodega("detal");
                                        $inventarioMovimiento->setInformacion("Ingreso por cambio manual de Despachado a Por Despachar en orden VRX-".$despachoOrden->getId());
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
                            $items_str = $items_str.'<tr>
                                <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getProducto()->getProducto()->getNombre().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getProducto()->getProducto()->getReferencia().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getProducto()->getNombre().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getColor().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getCantidad().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">$'.$item->getPrecio().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">$'.($item->getPrecio()*(int)$item->getCantidad()).'</td>
                            </tr>';
                        }
                        else{
                            $product_id = explode("-",$key)[1];
                            $producto = $this->getDoctrine()->getRepository(ProductoTalla::class)->find($product_id);
                            $color = $em->getRepository('AppBundle:ProductoColor')->findOneBy(array('producto' => $producto->getProducto(),'nombre' => $request->request->get('color-'.$product_id.'-'.$counter.'-new')));
                            $existencia = $em->getRepository('AppBundle:ProductoInventario')->findOneBy(array('producto' => $producto,'color' => $color));
                            //dump($existencia);exit;
                            if($despachoOrden->getClienteTipo()=="MAYORISTA" && $existencia->getQtyActualMayorista()<$request->request->get('producto-'.$product_id.'-'.$counter.'-new')){
                                $this->addFlash(
                                    'error',
                                    'El producto '.$producto->getProducto()->getNombre().' Talla: '.$producto->getNombre().' No tiene la disponibilidad suficiente en el inventario'
                                );
                                return $this->redirectToRoute('despachoorden_index');
                            }
                            else if($despachoOrden->getClienteTipo()=="DETAL" && $existencia->getQtyActualDetal()<$request->request->get('producto-'.$product_id.'-'.$counter.'-new')){
                                $this->addFlash(
                                    'error',
                                    'El producto '.$producto->getProducto()->getNombre().' Talla: '.$producto->getNombre().' No tiene la disponibilidad suficiente en el inventario'
                                );
                                return $this->redirectToRoute('despachoorden_index');
                            }
                            $orden_item = new DespachoOrdenItem();
                            $orden_item->setProducto($producto);
                            $orden_item->setColor($request->request->get('color-'.$product_id.'-'.$counter.'-new'));
                            $orden_item->setOrdenDespacho($despachoOrden);
                            $orden_item->setCantidad($request->request->get('producto-'.$product_id.'-'.$counter.'-new'));
                            $orden_item->setPrecio($request->request->get('precio-'.$product_id.'-'.$counter.'-new'));
                            $orden_item->setBodega($request->request->get('appbundle_despachoorden')['clienteTipo']);
                            $orden_item->setEstatus(1);
                            $em->persist($orden_item);
                            $em->flush();
                            $items_str = $items_str.'<tr>
                                <td style="text-align: center;border:1px solid black;padding:10px;">'.$orden_item->getProducto()->getProducto()->getNombre().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">'.$orden_item->getProducto()->getProducto()->getReferencia().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">'.$orden_item->getProducto()->getNombre().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">'.$orden_item->getColor().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">'.$orden_item->getCantidad().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">$'.$orden_item->getPrecio().'</td>
                                <td style="text-align: center;border:1px solid black;padding:10px;">$'.($orden_item->getPrecio()*(int)$item->getCantidad()).'</td>
                            </tr>';
                        }

                    }
                }
                //enviar correo al admin
                try {
                    $url = "https://www.veroxcloset.com/send-email";//CAMBIAR EN PRODUCCION
                    $client2 = new Client();
                    $options = [
                        'form_params' => [
                            "json_string" => json_encode([
                                "email_to"=>"disenograficovrx@gmail.com",
                                "email_cc"=>"veroxcloset@veroxcloset.com",
                                //"email_to"=>"jabarragan182@gmail.com",
                                //"email_cc"=>"",
                                "email_bcc"=>"",
                                "email_subject"=>"MODIFICACION DE ORDEN DE DESPACHO VRX-".$despachoOrden->getId(),
                                "email_body"=>'<h1>Se ha modificado la orden de despacho!</h1>
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
                                    </tr>'.$items_str.'
                                </table>
                                <br>
                                <h3>Usuario: '.$despachoOrden->getClienteId()->getNombre().' '.$despachoOrden->getClienteId()->getApellidos().'</h3>
                                <h3>Modificado por: '.$user->getName().'</h3>
                                <h3>Dirección de envío: '.$despachoOrden->getDireccionEnvio().'</h3>
                                <h3>Costo Envío: $'.$despachoOrden->getCostoEnvio().'</h3>
                                <h3>Total: $'.$despachoOrden->getTotal().'</h3>
                                <h3>Medio de Pago: '.$despachoOrden->getTipoPago().'</h3>
                                <h3>Referencia del pedido: VRX-'.$despachoOrden->getId().'</h3>
                                <h3>Anotaciones: '.$despachoOrden->getNotas().'</h3><br>
                                <p>Revisa en la plataforma de administración toda la información del pedido.</p><br>
                                <h3>Atentamente,</h3>
                                <h4>VEROXCLOSET<h4>'
                            ])
                        ]
                    ];
                    $client2->post($url,$options);
                } catch (\Throwable $th) {
                    //throw $th;
                }
                
                $this->addFlash(
                    'success',
                    'Registro editado correctamente'
                );
                return $this->redirectToRoute('despachoorden_index');
            }
            return $this->render('despachoorden/edit.html.twig', array(
                'despachoOrden' => $despachoOrden,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                "items"=>$items,
                "colors"=>$colors
            ));
        } catch (\Throwable $th) {
            dump($th);exit;
        }
    }

    /**
     * Deletes a despachoOrden entity.
     *
     */
    public function deleteAction(Request $request, DespachoOrden $despachoOrden)
    {
        $form = $this->createDeleteForm($despachoOrden);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $items_str = "";
            $orden_items = $em->getRepository('AppBundle:DespachoOrdenItem')->createQueryBuilder('a')
            ->where('a.ordenDespacho = :orden')->setParameter('orden', $despachoOrden->getId())
            ->getQuery()
            ->getResult();
            foreach ($orden_items as $item) {
                $items_str =$items_str.'<tr>
                    <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getProducto()->getProducto()->getNombre().'</td>
                    <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getProducto()->getProducto()->getReferencia().'</td>
                    <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getProducto()->getNombre().'</td>
                    <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getColor().'</td>
                    <td style="text-align: center;border:1px solid black;padding:10px;">'.$item->getCantidad().'</td>
                    <td style="text-align: center;border:1px solid black;padding:10px;">$'.$item->getPrecio().'</td>
                    <td style="text-align: center;border:1px solid black;padding:10px;">$'.($item->getPrecio()*(int)$item->getCantidad()).'</td>
                </tr>';
            }
            try {
                $url = "https://www.veroxcloset.com/send-email";
                $client3 = new Client();
                $options = [
                    'form_params' => [
                        "json_string" => json_encode([
                            "email_to"=>"disenograficovrx@gmail.com",
                            "email_cc"=>"veroxcloset@veroxcloset.com",
                            // "email_to"=>"jabarragan182@gmail.com",
                            // "email_cc"=>"",
                            "email_bcc"=>"",
                            "email_subject"=>"Orden de despacho eliminada: VRX-".$despachoOrden->getId(),
                            "email_body"=>'<h1>Se ha eliminado la orden de despacho!</h1>
                            <span>A continuación los datos de la orden:</span><br><br>
                            <span>Eliminada por: '.$user->getName().'</span>
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
                $client3->post($url,$options);
            } catch (\Throwable $th) {
                //throw $th;
            }
            
            $em->remove($despachoOrden);
            $em->flush($despachoOrden);
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

    public function pagadoAction(DespachoOrden $despachoOrden){
        $em = $this->getDoctrine()->getManager();
        $despachoOrden->setStatusPago(2);
        $em->persist($despachoOrden);
        $em->flush($despachoOrden);
        $deleteForm = $this->createDeleteForm($despachoOrden);
        $items = $em->getRepository('AppBundle:DespachoOrdenItem')
              ->createQueryBuilder('a')
              ->where('a.ordenDespacho = :orden')
              ->setParameter('orden', $despachoOrden)
              ->getQuery()
              ->getResult();

        $this->addFlash(
            'success',
            'Registro editado correctamente'
        );
        return $this->render('despachoorden/show.html.twig', array(
            'despachoOrden' => $despachoOrden,
            'delete_form' => $deleteForm->createView(),
            "items"=>$items
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
                        <a href="https://www.fedex.com/en-us/tracking.html">Fedex tracking</a>
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

        $this->addFlash(
            'success',
            'Registro editado correctamente'
        );
        return $this->render('despachoorden/show.html.twig', array(
            'despachoOrden' => $despachoOrden,
            'delete_form' => $deleteForm->createView(),
            "items"=>$items
        ));
    }
}
