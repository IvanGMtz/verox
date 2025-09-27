<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DespachoOrden;
use AppBundle\Entity\DespachoOrdenItem;
use AppBundle\Entity\FosUser;
use AppBundle\Entity\Producto;
use AppBundle\Entity\ProductoCategoria;
use AppBundle\Entity\StoreBonos;
use AppBundle\Entity\StoreSeo;
use AppBundle\Entity\StoreUsuarios;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Store controller.
 *
 */
class StoreController extends Controller
{
    public function configInitAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $host = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $info = $em->getRepository('AppBundle:StoreInicio')
                ->createQueryBuilder('a')
                ->getQuery()
                ->getOneOrNullResult();
        $seo = $em->getRepository(StoreSeo::class)->find(1);
        $data=[
            "imagen_fondo"=>$host."/uploads/images/store_inicio/".$info->getImagenFondo(),
            "imagen_vrx"=>$host."/uploads/images/store_inicio/".$info->getImagenVrx(),
            "imagen_kiwi"=>$host."/uploads/images/store_inicio/".$info->getImagenKiwi(),
            "fuente"=>$info->getFuente(),
            "hex_fuente"=>$info->getHexFuente(),
            "hex_modal_body"=>$info->getHexModalBody(),
            "hex_modal_header"=>$info->getHexModalHeader(),
            "facebook_pixel"=>$seo->getFacebookPixel(),
            "google_adds"=>$seo->getGoogleAnalytics(),
            "meta-description"=>$seo->getKeyWords(),
        ];
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function configStoreAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $marca = $request->query->get('marca');
        $host = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $info = $em->getRepository('AppBundle:StoreTienda')
                ->createQueryBuilder('a')
                ->where('a.nombre = :nombre')
                ->setParameter('nombre', $marca)
                ->getQuery()
                ->getOneOrNullResult();
        $slider = $em->getRepository('AppBundle:StoreTiendaSlider')
                ->createQueryBuilder('a')
                ->where('a.store = :store')
                ->setParameter('store', $info)
                ->getQuery()
                ->getResult();
        $seo = $em->getRepository(StoreSeo::class)->find(1);
        $data=[
            "fuente_navbar"=>$info->getFuenteNavbar(),
            "logo_navbar"=>$host."/uploads/images/store_tienda/".$info->getLogoNavbar(),
            "mayoristas_imagen"=>$host."/uploads/images/store_tienda/".$info->getMayoristasImagen(),
            "mayoristas_imagen2"=>$host."/uploads/images/store_tienda/".$info->getMayoristasImagen2(),
            "mayoristas_imagen3"=>$host."/uploads/images/store_tienda/".$info->getMayoristasImagen3(),
            "mayoristas_imagen4"=>$host."/uploads/images/store_tienda/".$info->getMayoristasImagen4(),
            "mayoristas_imagen5"=>$host."/uploads/images/store_tienda/".$info->getMayoristasImagen5(),
            "hex_fuente_navbar"=>$info->getHexFuenteNavbar(),
            "hex_fondo_navbar"=>$info->getHexFondoNavbar(),
            "whatsapp_main"=>$info->getWhatsappMainColor(),
            "whatsapp_text"=>$info->getWhatsappTextColor(),
            "hex_fondo_navbar"=>$info->getHexFondoNavbar(),
            "facebook_pixel"=>$seo->getFacebookPixel(),
            "google_adds"=>$seo->getGoogleAnalytics(),
            "meta-description"=>$seo->getKeyWords(),
            "slider"=>[],
            "categories"=>[]
        ];
        $categories = $em->getRepository('AppBundle:ProductoCategoria')
        ->createQueryBuilder('a')
        ->getQuery()
        ->getResult();
        foreach ($categories as $category) {
            array_push($data['categories'],[
                "nombre"=>$category->getNombreEs(),
                "descripcion"=>$category->getNombreEn(),
                "verox"=>$category->getVerox(),
                "kiwi"=>$category->getKiwi(),
                "orden"=>$category->getOrden(),
            ]);
        }
        usort($data['categories'], function($a, $b) {return ($a['orden']>$b['orden'])?1:-1;});
        foreach ($slider as $slide) {
            array_push($data['slider'],[
                "orden"=>$slide->getOrden(),
                "imagen"=>$host."/uploads/images/store_tienda/slider/".$slide->getImagen()
            ]);
        }
        $img_order = array_column($data['slider'], 'orden');
        array_multisort($img_order, SORT_ASC, $data['slider']);
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function categoriesAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $marca = $request->query->get('marca');
        $host = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $productos = $em->getRepository('AppBundle:ProductoInventario')
                ->createQueryBuilder('a')
                ->join('a.producto','p')
                ->join('p.producto','p2')
                ->where('p2.marca = :marca')
                ->setParameter('marca', $marca)
                ->andwhere('p2.estado = :status')
                ->setParameter('status', "DISPONIBLE")
                ->getQuery()
                ->getResult();
        $data = [];
        $data2=[];
        foreach ($productos as $producto) {
            $product = $em->getRepository(Producto::class)->find($producto->getProducto()->getProducto());
            if($product->getCategoria()->getPrincipal() == "Si"){
                $data[$product->getCategoria()->getId()] = [
                    "id" => $product->getCategoria()->getId(),
                    "nombre_es" => $product->getCategoria()->getNombreEs(),
                    "verox" => $product->getCategoria()->getVerox(),
                    "kiwi" => $product->getCategoria()->getKiwi(),
                    "orden" => $product->getCategoria()->getOrden(),
                    "imagen" => ($marca == 'VEROX') ? $host."/uploads/images/categorias/".$product->getCategoria()->getImagen() :$host."/uploads/images/categorias/".$product->getCategoria()->getImagen2()
                ];
            }
        }
        foreach ($data as $item) {
            array_push($data2,$item);
        }
        usort($data2, function($a, $b) {return ($a['orden']>$b['orden'])?1:-1;});
        $response = new Response(json_encode($data2));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function productsAction(Request $request,PaginatorInterface $paginator){
        try {
            $em = $this->getDoctrine()->getManager(); 
            $marca = $request->query->get('marca');
            $categoria = $request->query->get('categoria');
            $cliente_tipo = $request->query->get('cliente_tipo');
            $filtros = $request->query->get('filtros');
            $p_id = $request->query->get('id');
            $data = ["productos"=>[],"pagination"=>[]];
            $page = $request->query->getInt('page', 1);
            $host = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
            $query = $em->getRepository('AppBundle:ProductoInventario')->createQueryBuilder('a')
            #->join('a.producto','p')->join('a.color','c')->join('p.producto','p2')->where('p2.marca = :marca')->setParameter('marca', $marca);
            ->join('a.producto','p')->join('a.color','c')->join('p.producto','p2');
            if($categoria != ""){
                if($categoria=="NUEVA COLECCION"){
                    $query->andwhere('p2.etiqueta = :etiqueta')->setParameter('etiqueta', "NEW");
                }
                else{
                    $cat = $em->getRepository(ProductoCategoria::class)->findOneBy(['nombreEs'=>$categoria]);
                    $query->andwhere('p2.categoria = :categoria')->setParameter('categoria', $cat);
                }
            }
            if($filtros != ""){
                $parsed = explode(",",$filtros);
                foreach ($parsed as $filtro){
                    if($filtro!=""){
                        $parsed2 = explode(":",$filtro);
                        $tipo = $parsed2[0];
                        $filters = explode("-",$parsed2[1]);
                        if($tipo=="talla"){
                            foreach ($filters as $key=>$talla) {
                                if($key==0){
                                    $query->andWhere('p.nombre = :talla'.$key)->setParameter('talla'.$key, $talla);
                                }
                                else{
                                    $query->orWhere('p.nombre = :talla'.$key)->setParameter('talla'.$key, $talla);
                                }
                            }
                        }
                        elseif($tipo=="color"){
                            foreach ($filters as $key=>$color) {
                                if($key==0){
                                    $query->andWhere('c.nombre = :color'.$key)->setParameter('color'.$key, $color);
                                }
                                else{
                                    $query->orWhere('c.nombre = :color'.$key)->setParameter('color'.$key, $color);
                                }
                            }
                        }
                        elseif($tipo=="precio"){
                            $query->andWhere('p2.precioDetal >= :precio1')->setParameter('precio1', (int)$filters[0]);
                            $query->andWhere('p2.precioDetal <= :precio2')->setParameter('precio2', (int)$filters[1]);
                        }
                        elseif($tipo=="tipo"){
                            foreach ($filters as $key=>$tipo) {
                                $cat = $em->getRepository(ProductoCategoria::class)->findOneBy(['nombreEs'=>$tipo]);
                                if($key==0){
                                    $query->andwhere('p2.categoria = :categoria')->setParameter('categoria', $cat);
                                }
                                else{
                                    $query->orWhere('p2.categoria = :categoria')->setParameter('categoria', $cat);
                                }
                            }
                        }
                    }
                }
            }
            if($p_id !=""){
                $query->andwhere('p2.id = :id')->setParameter('id', $p_id);
            }
            $query->andWhere('p2.estado = :status')->setParameter('status', 'DISPONIBLE');
            if($cliente_tipo=="MAYORISTA"){
                $query->andWhere('a.qtyActualMayorista > :qty')->setParameter('qty', 0);
            }
            else{
                $query->andWhere('a.qtyActualDetal > :qty')->setParameter('qty', 0);
            }
            $productos = $query->getQuery()->getResult();
            $data['pagination']=[
                "total_registros"=>count($productos),
            ];         
            foreach ($productos as $producto) {
                $product = $em->getRepository(Producto::class)->find($producto->getProducto()->getProducto());
                $descripcion = $em->getRepository('AppBundle:ProductoDescripcion')
                    ->createQueryBuilder('a')
                    ->where('a.producto = :product')
                    ->setParameter('product', $producto->getProducto()->getProducto())
                    ->getQuery()
                    ->getOneOrNullResult();
                $data['productos'][$producto->getProducto()->getProducto()->getId()] = [
                    "id"=>$producto->getProducto()->getProducto()->getId(),
                    "nombre"=>$product->getNombre(),
                    "referencia"=>$product->getReferencia(),
                    "etiqueta"=>$product->getEtiqueta(),
                    "marca"=>$product->getMarca(),
                    "descripcion"=>$descripcion->getTexto(),
                    "estado"=>$product->getEstado(),
                    "categoria"=>$product->getCategoria()->getNombreEs(),
                    "categoria_descripcion" => $product->getCategoria()->getNombreEn(),
                    "precio_detal"=>$product->getPrecioDetal(),
                    "precio_mayorista"=>$product->getPrecioMayorista(),
                    "imagenes"=>[],
                    "inventario" => [],
                    "complementos" => [],
                ];
            }
            foreach ($productos as $producto) {
                $reserva_detal = 0;
                $reserva_mayorista = 0;
                $reservados = $em->getRepository('AppBundle:DespachoOrdenItem')
                    ->createQueryBuilder('a')
                    ->where('a.producto = :producto')
                    ->setParameter('producto', $producto->getProducto())
                    ->andwhere('a.color = :color')
                    ->setParameter('color', $producto->getColor()->getNombre())
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
                $qtyMayoristas = ($producto->getQtyActualMayorista() - $reserva_mayorista<0)?0:$producto->getQtyActualMayorista() - $reserva_mayorista;
                if($cliente_tipo=="MAYORISTA" && $producto->getQtyActualMayorista() - $reserva_mayorista > 0){
                    array_push($data['productos'][$producto->getProducto()->getProducto()->getId()]['inventario'],[
                        "talla"=>$producto->getProducto()->getNombre(),
                        "color"=>["nombre"=>$producto->getColor()->getNombre(),"hex"=>$producto->getColor()->getHex()],
                        "cantidad_detal"=>($qtyMayoristas <= 0)?0:$producto->getQtyActualDetal() - $reserva_detal,
                        "cantidad_mayorista"=>($qtyMayoristas<0)?0:$qtyMayoristas,
                        "reservados"=>[$reserva_mayorista,$reserva_detal]
                    ]);
                }
                else if($cliente_tipo=="DETAL" && $producto->getQtyActualDetal() - $reserva_detal > 0){
                    array_push($data['productos'][$producto->getProducto()->getProducto()->getId()]['inventario'],[
                        "talla"=>$producto->getProducto()->getNombre(),
                        "color"=>["nombre"=>$producto->getColor()->getNombre(),"hex"=>$producto->getColor()->getHex()],
                        "cantidad_detal"=>($qtyMayoristas <= 0)?0:$producto->getQtyActualDetal() - $reserva_detal,
                        "cantidad_mayorista"=>($qtyMayoristas<0)?0:$qtyMayoristas,
                        "reservados"=>[$reserva_mayorista,$reserva_detal]
                    ]);
                }
            }
            foreach ($data['productos'] as $key=>$producto) {
                $imagenes = $em->getRepository('AppBundle:ProductoImagen')
                    ->createQueryBuilder('a')
                    ->where('a.producto = :producto')
                    ->setParameter('producto', $producto['id'])
                    ->orderBy('a.orden', 'ASC')
                    ->getQuery()
                    ->getResult();
                foreach ($imagenes as $imagen) {
                    array_push($data['productos'][$key]['imagenes'],[
                        "URL"=>$host."/uploads/images/productos/".$imagen->getImagen(),
                        "color"=>$imagen->getColor()
                    ]);
                }
                $complementos = $em->getRepository('AppBundle:ProductoComplemento')
                ->createQueryBuilder('a')
                ->where('a.producto = :product')
                ->setParameter('product', $producto['id'])
                ->getQuery()
                ->getResult();
                foreach ($complementos as $complemento) {
                    try {
                        $complement = $em->getRepository(Producto::class)->find($complemento->getComplemento());
                        //$complement_talla = $em->getRepository(Producto::class)->find($complemento->getComplemento());
                        $imagen = $em->getRepository('AppBundle:ProductoImagen')
                        ->createQueryBuilder('a')
                        ->where('a.producto = :producto')
                        ->setParameter('producto', $complement->getId())
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();
                        $complementInventario = $em->getRepository('AppBundle:ProductoInventario')->createQueryBuilder('a')
                        ->join('a.producto','p')->join('a.color','c')->join('p.producto','p2')
                        ->andWhere('p2.estado = :status')->setParameter('status', 'DISPONIBLE')
                        ->andWhere('a.qtyActualDetal > :qty OR a.qtyActualMayorista > :qty')->setParameter('qty', 0)
                        ->andWhere('p2.referencia = :search')->setParameter('search', $complement->getReferencia())
                        ->getQuery()->getResult()
                        ;
                        $complementQty = 0;
                        foreach ($complementInventario as $inventario) {
                            $complementQty = ($cliente_tipo=="MAYORISTA") ? $complementQty + $inventario->getQtyActualMayorista() : $complementQty + $inventario->getQtyActualDetal();
                        }
                        if($complementQty > 0){
                            array_push($data['productos'][$key]['complementos'],[
                                "id"=>$complement->getId(),
                                "nombre"=>$complement->getNombre(),
                                "referencia"=>$complement->getReferencia(),
                                "etiqueta"=>$complement->getEtiqueta(),
                                "precio_detal"=>$complement->getPrecioDetal(),
                                "precio_mayorista"=>$complement->getPrecioMayorista(),
                                "imagenes"=>[ ($imagen) ? $host."/uploads/images/productos/".$imagen->getImagen() : ""
                                ],
                            ]);
                        }
                    } catch (\Throwable $th) {
                        dump($complement->getId());
                    }
                }
            }
            $data['productos'] = array_filter($data['productos'],function($item){
                return count($item["inventario"])>0;
            });
            $response = new Response(json_encode($data));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (\Throwable $th) {
            dump($th);exit;
        }
    }
    public function send_productAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $marca = $request->query->get('marca');
        $data = [];
        $host = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $productos = $em->getRepository('AppBundle:ProductoInventario')
                ->createQueryBuilder('a')
                ->join('a.producto','p')
                ->join('p.producto','p2')
                // ->where('p2.marca = :marca')
                // ->setParameter('marca', $marca)
                ->getQuery()
                ->getResult();
        //dump($productos);exit;
        foreach ($productos as $producto) {
            $product = $em->getRepository(Producto::class)->find($producto->getProducto()->getProducto());
            $descripcion = $em->getRepository('AppBundle:ProductoDescripcion')
                ->createQueryBuilder('a')
                ->where('a.producto = :product')
                ->setParameter('product', $producto->getProducto())
                ->getQuery()
                ->getOneOrNullResult();
            $data[$producto->getProducto()->getId()] = [
                "id"=>$producto->getProducto()->getId(),
                "nombre"=>$product->getNombre(),
                "referencia"=>$product->getReferencia(),
                "etiqueta"=>$product->getEtiqueta(),
                "marca"=>$product->getMarca(),
                "descripcion"=>($descripcion)?$descripcion->getTexto():"",
                "estado"=>$product->getEstado(),
                "categoria"=>$product->getCategoria()->getNombreEs(),
                "categoria_descripcion" => $product->getCategoria()->getNombreEn(),
                "precio_detal"=>$product->getPrecioDetal(),
                "precio_mayorista"=>$product->getPrecioMayorista(),
                "imagenes"=>[],
                "inventario" => []
            ];
        }
        foreach ($productos as $producto) { 
            $reserva_detal = 0;
            $reserva_mayorista = 0;
            $reservados = $em->getRepository('AppBundle:DespachoOrdenItem')
                ->createQueryBuilder('a')
                ->where('a.producto = :producto')
                ->setParameter('producto', $producto->getProducto())
                ->andwhere('a.color = :color')
                ->setParameter('color', $producto->getColor()->getNombre())
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
            $qtyMayoristas = ($producto->getQtyActualMayorista() - $reserva_mayorista < 0) ? 0 : $producto->getQtyActualMayorista() - $reserva_mayorista;
            array_push($data[$producto->getProducto()->getId()]['inventario'],[
                "talla"=>$producto->getProducto()->getNombre(),
                "color"=>["nombre"=>$producto->getColor()->getNombre(),"hex"=>$producto->getColor()->getHex()],
                "cantidad_detal"=>$qtyMayoristas <= 0 ? 0 : $producto->getQtyActualDetal() - $reserva_detal,
                "cantidad_mayorista"=>$qtyMayoristas,
            ]);
        }
        foreach ($data as $key=>$producto) {
            $imagenes = $em->getRepository('AppBundle:ProductoImagen')
                ->createQueryBuilder('a')
                ->where('a.producto = :producto')
                ->setParameter('producto', $producto['id'])
                ->orderBy('a.orden', 'ASC')
                ->getQuery()
                ->getResult();
            foreach ($imagenes as $imagen) {
                array_push($data[$key]['imagenes'],$host."/uploads/images/productos/".$imagen->getImagen());
            }
        }
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    public function search_productAction(Request $request,PaginatorInterface $paginator){
        try {
            $em = $this->getDoctrine()->getManager();
            $marca = $request->query->get('marca');
            $search = $request->query->get('search');
            $data = ["productos"=>[],"pagination"=>[]];
            $page = $request->query->getInt('page', 1);
            $host = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
            $query = $em->getRepository('AppBundle:ProductoInventario')->createQueryBuilder('a');
            if($search!=""){
                $query
                //->join('a.producto','p')->join('a.color','c')->join('p.producto','p2')->where('p2.marca = :marca')->setParameter('marca', $marca)
                ->join('a.producto','p')->join('a.color','c')->join('p.producto','p2')
                ->andWhere('p2.estado = :status')->setParameter('status', 'DISPONIBLE')
                ->andWhere('a.qtyActualDetal > :qty OR a.qtyActualMayorista > :qty')->setParameter('qty', 0)
                ->andWhere('p2.nombre LIKE :search OR p2.referencia LIKE :search')->setParameter('search', '%'.$search.'%')
                ;
            }
            $productos = $query->getQuery()->getResult();
            $data['pagination']=[
                "total_registros"=>count($productos),
            ];
                    
            foreach ($productos as $producto) {
                $product = $em->getRepository(Producto::class)->find($producto->getProducto()->getProducto());
                $descripcion = $em->getRepository('AppBundle:ProductoDescripcion')
                    ->createQueryBuilder('a')
                    ->where('a.producto = :product')
                    ->setParameter('product', $producto->getProducto()->getProducto())
                    ->getQuery()
                    ->getOneOrNullResult();
                $data['productos'][$producto->getProducto()->getProducto()->getId()] = [
                    "id"=>$producto->getProducto()->getProducto()->getId(),
                    "nombre"=>$product->getNombre(),
                    "referencia"=>$product->getReferencia(),
                    "etiqueta"=>$product->getEtiqueta(),
                    "marca"=>$product->getMarca(),
                    "descripcion"=>$descripcion->getTexto(),
                    "estado"=>$product->getEstado(),
                    "categoria"=>$product->getCategoria()->getNombreEs(),
                    "categoria_descripcion" => $product->getCategoria()->getNombreEn(),
                    "precio_detal"=>$product->getPrecioDetal(),
                    "precio_mayorista"=>$product->getPrecioMayorista(),
                    "imagenes"=>[],
                    "inventario" => [],
                    "complementos" => [],
                ];
            }
            
            foreach ($productos as $producto) {
                array_push($data['productos'][$producto->getProducto()->getProducto()->getId()]['inventario'],[
                    "talla"=>$producto->getProducto()->getNombre(),
                    "color"=>["nombre"=>$producto->getColor()->getNombre(),"hex"=>$producto->getColor()->getHex()],
                    "cantidad_detal"=>$producto->getQtyActualDetal(),
                    "cantidad_mayorista"=>$producto->getQtyActualMayorista()
                ]);
            }
            foreach ($data['productos'] as $key=>$producto) {
                $imagenes = $em->getRepository('AppBundle:ProductoImagen')
                    ->createQueryBuilder('a')
                    ->where('a.producto = :producto')
                    ->setParameter('producto', $producto['id'])
                    ->orderBy('a.orden', 'ASC')
                    ->getQuery()
                    ->getResult();
                foreach ($imagenes as $imagen) {
                    array_push($data['productos'][$key]['imagenes'],[
                        "URL"=>$host."/uploads/images/productos/".$imagen->getImagen(),
                        "color"=>$imagen->getColor()
                    ]);
                }
                $complementos = $em->getRepository('AppBundle:ProductoComplemento')
                ->createQueryBuilder('a')
                ->where('a.producto = :product')
                ->setParameter('product', $producto['id'])
                ->getQuery()
                ->getResult();
                foreach ($complementos as $complemento) {
                    $complement = $em->getRepository(Producto::class)->find($complemento->getComplemento());
                    $imagen = $em->getRepository('AppBundle:ProductoImagen')
                        ->createQueryBuilder('a')
                        ->where('a.producto = :producto')
                        ->setParameter('producto', $complement->getId())
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();
                    array_push($data['productos'][$key]['complementos'],[
                        "id"=>$complement->getId(),
                        "nombre"=>$complement->getNombre(),
                        "referencia"=>$complement->getReferencia(),
                        "etiqueta"=>$complement->getEtiqueta(),
                        "precio_detal"=>$complement->getPrecioDetal(),
                        "precio_mayorista"=>$complement->getPrecioMayorista(),
                        "imagenes"=>[ ($imagen) ? $host."/uploads/images/productos/".$imagen->getImagen() : ""],
                    ]);
                }
            }
            $response = new Response(json_encode($data));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (\Throwable $th) {
            dump($th);
        }
    }
    public function new_orderAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $data= json_decode($request->getContent(),true);
            //revisar si el usuario existe
            $user = $em->getRepository(StoreUsuarios::class)->createQueryBuilder('a')
            ->where('a.email = :mail')->setParameter('mail', $data['usuario']['email'])
            ->getQuery()
            ->getOneOrNullResult();
            if($user==null){
                $new_user = new StoreUsuarios();
                $new_user->setEmail($data['usuario']['email']);
                $new_user->setNombre($data['usuario']['name']);
                $new_user->setApellidos($data['usuario']['last_name']);
                $new_user->setIdn($data['usuario']['idn']);
                $new_user->setTelefono($data['usuario']['phone']);
                $new_user->setDireccion($data['usuario']['address']);
                $new_user->setEstado($data['usuario']['state']);
                $new_user->setWebId($data['usuario']['webId']);
                $new_user->setTipo('DETAL');
                $em->persist($new_user);
                $em->flush();
                $user = $new_user;
            }
            else{
                $user->setDireccion($data['usuario']['address']);
                $user->setEstado($data['usuario']['state']);
                if(!$user->getIdn()){
                    $user->setIdn($data['usuario']['idn']);
                }
                if(!$user->getTelefono()){
                    $user->setTelefono($data['usuario']['phone']);
                }
                $em->persist($user);
                $em->flush();
            }
            //revisar los productos del pedido
            $bono_comment = "";
            $freeShipping = false;
            $productos_check = true;
            $subtotal = 0;
            $product_errors = [];
            foreach ($data['pedido']['productos'] as $producto) {
                $product = $em->getRepository('AppBundle:ProductoInventario')->createQueryBuilder('a')
                ->join('a.producto','p')->join('a.color','c')->join('p.producto','p2')
                ->where('p2.id = :id')->setParameter('id', $producto['product_id'])
                ->andWhere('p.nombre = :talla')->setParameter('talla', $producto['talla'])
                ->andWhere('c.nombre = :color')->setParameter('color', $producto['color'])
                ->getQuery()
                ->getOneOrNullResult();
                $reservados = $em->getRepository('AppBundle:DespachoOrdenItem')
                    ->createQueryBuilder('a')
                    ->where('a.producto = :producto')
                    ->setParameter('producto', $product->getProducto()->getId())
                    ->andwhere('a.color = :color')
                    ->setParameter('color', $producto['color'])
                    ->andwhere('a.estatus = :estatus')
                    ->setParameter('estatus', 1)
                    ->getQuery()
                    ->getResult();
                $reserva_detal = 0;
                $reserva_mayorista = 0;
                $reservados_data = [];
                foreach ($reservados as $reservado) {
                    if($reservado->getBodega()=="DETAL"){
                        $reserva_detal = $reserva_detal + $reservado->getCantidad();
                    }
                    else{
                        $reserva_mayorista = $reserva_mayorista + $reservado->getCantidad();
                        array_push($reservados_data,$reservado->getId());
                    }
                }
                if($product != null){
                    //comparar segun tipo de cliente
                    if($data['usuario']['type'] == 'DETAL'){
                        //revisar bono
                        if($data['bono']){
                            $value = (int)$producto['cantidad'] * (float)$product->getProducto()->getProducto()->getPrecioDetal();
                            if(($producto['product_id'] == $data['bono']['product'] || $data['bono']['product'] == "Todos")&&($producto['categoria'] == $data['bono']['category'] || $data['bono']['category'] == "Todos")){
                                if((float)$data['bono']['value']<1){
                                    $subtotal += $value - ($value * (float)$data['bono']['value']);
                                    $bono_comment="Bono de descuento de ".((float)$data['bono']['value']*100)."% en producto REF# ".$product->getProducto()->getProducto()->getReferencia();
                                }
                                else{
                                    $subtotal+= $value - ((float)$data['bono']['value']);
                                    $bono_comment="Bono de descuento de $".$data['bono']['value']." en producto REF# ".$product->getProducto()->getProducto()->getReferencia();
                                }
                            }
                            else{
                                $subtotal += $value;
                            }
                            if($data['bono']['free_shipping']){
                                $freeShipping = true;
                            }
                            $productos_check = ((int)$product->getQtyActualDetal() - $reserva_detal >= (int)$producto['cantidad']) ? true : false;
                            if(!$productos_check){
                                array_push($product_errors,$producto['nombre']." Talla ".$producto['talla']." Color ".$producto['color']." Qty Actual: ".(int)$product->getQtyActualDetal()." Qty Reservada: ".$reserva_detal); 
                            }
                        }
                        else{
                            $subtotal += (int)$producto['cantidad'] * (float)$product->getProducto()->getProducto()->getPrecioDetal();
                            $productos_check = ((float)$product->getProducto()->getProducto()->getPrecioDetal() === (float)$producto['precio']) ? true : false;
                            if(!$productos_check){
                                array_push($product_errors,$producto['nombre']." Talla ".$producto['talla']." Color ".$producto['color']." Qty Actual: ".(int)$product->getQtyActualMayorista()." Qty Reservada: ".$reserva_mayorista); 
                            }
                            $productos_check = ((int)$product->getQtyActualDetal() - $reserva_detal >= (int)$producto['cantidad']) ? true : false;
                            if(!$productos_check){
                                array_push($product_errors,$producto['nombre']." Talla ".$producto['talla']." Color ".$producto['color']." Qty Actual: ".(int)$product->getQtyActualDetal()." Qty Reservada: ".$reserva_detal); 
                            }
                        }
                    }
                    else{
                        //revisar bono
                        if($data['bono']){
                            $value = (int)$producto['cantidad'] * (float)$product->getProducto()->getProducto()->getPrecioMayorista();
                            if(($producto['product_id'] == $data['bono']['product'] || $data['bono']['product'] == "Todos")&&($producto['categoria'] == $data['bono']['category'] || $data['bono']['category'] == "Todos")){
                                if((float)$data['bono']['value']<1){
                                    $subtotal += $value - ($value * (float)$data['bono']['value']);
                                    $bono_comment="Bono de descuento de ".((float)$data['bono']['value']*100)."% en producto REF# ".$product->getProducto()->getProducto()->getReferencia();
                                }
                                else{
                                    $subtotal+= $value - ((float)$data['bono']['value']);
                                    $bono_comment="Bono de descuento de $".$data['bono']['value']." en producto REF# ".$product->getProducto()->getProducto()->getReferencia();
                                }
                                if($data['bono']['free_shipping']){
                                    $freeShipping = true;
                                }
                            }
                            else{
                                $subtotal += $value;
                            }
                            $productos_check = ((int)$product->getQtyActualMayorista() - $reserva_mayorista >= (int)$producto['cantidad']) ? true : false;
                            if(!$productos_check){
                                array_push($product_errors,$producto['nombre']." Talla ".$producto['talla']." Color ".$producto['color']." Qty Actual: ".(int)$product->getQtyActualMayorista()." Qty Reservada: ".$reserva_mayorista); 
                            }
                        }
                        else{
                            $subtotal += (int)$producto['cantidad'] * (float)$product->getProducto()->getProducto()->getPrecioMayorista();
                            $productos_check = ((float)$product->getProducto()->getProducto()->getPrecioMayorista() === (float)$producto['precio']) ? true : false;
                            if(!$productos_check){
                                array_push($product_errors,$producto['nombre']." Talla ".$producto['talla']." Color ".$producto['color']." Qty Actual: ".(int)$product->getQtyActualMayorista()." Qty Reservada: ".$reserva_mayorista); 
                            }
                            $productos_check = ((int)$product->getQtyActualMayorista() - $reserva_mayorista >= (int)$producto['cantidad']) ? true : false;
                            if(!$productos_check){
                                array_push($product_errors,$producto['nombre']." Talla ".$producto['talla']." Color ".$producto['color']." Qty Actual: ".(int)$product->getQtyActualMayorista()." Qty Reservada: ".$reserva_mayorista); 
                            }
                        } 
                    }
                }
                else{
                    $response = new Response('{"result":"fail","reason":"Conflicto en la de reserva de productos, espere unos segundos y vuelva a enviar la orden"');
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            }
            $fecha = new \DateTime();
            if($productos_check && count($product_errors) < 1){
                // crear orden de despacho
                $despachoOrden = new DespachoOrden();
                $despachoOrden->setClienteId($user);
                $despachoOrden->setClienteTipo($data['usuario']['type']);
                $despachoOrden->setDireccionEnvio((array_key_exists('name', $data['envio']))?$data['envio']['name']." ".$data['envio']['last_name']." ".$data['envio']['address']:$user->getDireccion());
                $despachoOrden->setTipoPago($data['pago']);
                $despachoOrden->setStatusPago(1);
                $despachoOrden->setStatusOrden(1);
                if($freeShipping){
                    $despachoOrden->setCostoEnvio(0);
                }
                else{
                    $despachoOrden->setCostoEnvio(($subtotal>150&&$data['usuario']['type']=="DETAL")?0:(float)$data['envio']['price']);
                }
                if($data['bono']){
                    if(($data['bono']['product']=="Todos"&&$data['bono']['category'] == "Todos")){
                        if((float)$data['bono']['value']<1){
                            if($freeShipping){
                                $despachoOrden->setNotas($data['notas']." // "."Bono de descuento de ".((float)$data['bono']['value']*100)."%"." + FREE SHIPPING");
                            }
                            else{
                                $despachoOrden->setNotas($data['notas']." // "."Bono de descuento de ".((float)$data['bono']['value']*100)."%");
                            }
                        }
                        else{
                            if($freeShipping){
                                $despachoOrden->setNotas($data['notas']." // "."Bono de descuento de $".$data['bono']['value']." + FREE SHIPPING");
                            }
                            else{
                                $despachoOrden->setNotas($data['notas']." // "."Bono de descuento de $".$data['bono']['value']);
                            }
                        }
                    }
                    else if($data['bono']['category'] != "Todos"){
                        if((float)$data['bono']['value']<1){
                            if($freeShipping){
                                $despachoOrden->setNotas($data['notas']." // "."Bono de descuento de ".((float)$data['bono']['value']*100)."% en productos de categoria ".$data['bono']['category']." + FREE SHIPPING");
                            }
                            else{
                                $despachoOrden->setNotas($data['notas']." // "."Bono de descuento de ".((float)$data['bono']['value']*100)."% en productos de categoria ".$data['bono']['category']);
                            }
                        }
                        else{
                            if($freeShipping){
                                $despachoOrden->setNotas($data['notas']." // "."Bono de descuento de $".$data['bono']['value']." en productos de categoria ".$data['bono']['category']." + FREE SHIPPING");
                            }
                            else{
                                $despachoOrden->setNotas($data['notas']." // "."Bono de descuento de $".$data['bono']['value']." en productos de categoria ".$data['bono']['category']);
                            }
                        }
                    }
                    else if($data['bono']['product'] != "Todos"){
                        if($freeShipping){
                            $despachoOrden->setNotas($data['notas']." // ".$bono_comment." + FREE SHIPPING");
                        }
                        else{
                            $despachoOrden->setNotas($data['notas']." // ".$bono_comment);
                        }
                    }
                    $despachoOrden->setTotal((float)$data['envio']['price'] + $subtotal);
                }
                else{
                    $despachoOrden->setTotal((float)$data['envio']['price'] + $subtotal);
                    $despachoOrden->setNotas($data['notas']);
                }
                $despachoOrden->setUsuarioCreacion($em->getRepository(FosUser::class)->find(334));
                $despachoOrden->setFechaCreacion($fecha);
                $em->persist($despachoOrden);
                $em->flush();
                $items_str='';
                foreach ($data['pedido']['productos'] as $producto) {
                    $productInv = $em->getRepository('AppBundle:ProductoInventario')->createQueryBuilder('a')
                    ->join('a.producto','p')->join('a.color','c')->join('p.producto','p2')
                    ->where('p2.id = :id')->setParameter('id', $producto['product_id'])
                    ->andWhere('p.nombre = :talla')->setParameter('talla', $producto['talla'])
                    ->andWhere('c.nombre = :color')->setParameter('color', $producto['color'])
                    ->getQuery()
                    ->getOneOrNullResult();
                    $product = $productInv->getProducto();
                    $precio = ($data['usuario']['type']=="DETAL")?$product->getProducto()->getPrecioDetal():$product->getProducto()->getPrecioMayorista();
                    $orden_item = new DespachoOrdenItem();
                    $orden_item->setProducto($product);
                    $orden_item->setColor($producto['color']);
                    $orden_item->setOrdenDespacho($despachoOrden);
                    $orden_item->setCantidad((int)$producto['cantidad']);
                    $orden_item->setPrecio($precio);
                    $orden_item->setBodega($data['usuario']['type']);
                    $orden_item->setEstatus(1);
                    $em->persist($orden_item);
                    $em->flush();
                    
                    $items_str = $items_str.'<tr>
                        <td style="text-align: center;border:1px solid black;padding:10px;">'.$product->getProducto()->getNombre().'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">'.$producto['product_ref'].'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">'.$producto['talla'].'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">'.$producto['color'].'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">'.$producto['cantidad'].'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">$'.$precio.'</td>
                        <td style="text-align: center;border:1px solid black;padding:10px;">$'.($precio*(int)$producto['cantidad']).'</td>
                    </tr>';
                }
                //enviar correo al admin
                $url = "https://www.veroxcloset.com/send-email";//CAMBIAR EN PRODUCCION
                $client2 = new Client();
                $options = [
                    'form_params' => [
                        "json_string" => json_encode([
                            "email_to"=>"disenograficovrx@gmail.com",
                            "email_cc"=>"veroxcloset@veroxcloset.com",
                            "email_bcc"=>"",
                            "email_subject"=>"TIENES UN NUEVO PEDIDO EN VEROXCLOSET",
                            "email_body"=>'<h1>Un cliente ha realizado un nuevo pedido!</h1>
                            <span>A continuación los datos de la orden:</span><br><br>
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
                            <h3>Usuario: '.$user->getNombre().' '.$user->getApellidos().'</h3>
                            <h3>Dirección de envío: '.$despachoOrden->getDireccionEnvio().'</h3>
                            <h3>Costo Envío: $'.$despachoOrden->getCostoEnvio().'</h3>
                            <h3>Total: $'.$despachoOrden->getTotal().'</h3>
                            <h3>Medio de Pago: '.$data['pago'].'</h3>
                            <h3>Referencia del pedido: VRX-'.$despachoOrden->getId().'</h3>
                            <h3>Anotaciones: '.$despachoOrden->getNotas().'</h3><br>
                            <p>Revisa en la plataforma de administración toda la información del pedido.</p><br>
                            <h3>Atentamente,</h3>
                            <h4>VEROXCLOSET<h4>'
                        ])
                    ]
                ];
                $client2->post($url,$options);

            }
            if($productos_check && count($product_errors) < 1){ 
                $response = new Response('{"result":"'.$productos_check.'","order_id":"'.$despachoOrden->getId().'","total":"'.$despachoOrden->getTotal().'"}');
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else{
                $response = new Response('{"result":"false","errors":'.json_encode($product_errors).'}');
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        } catch (\Throwable $th) {
            dump(json_decode($request->getContent(),true),$th);exit;
        }
    }
    public function paypal_paymentAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $data= json_decode($request->getContent(),true);
        $order = $em->getRepository(DespachoOrden::class)->createQueryBuilder('a')
                ->where('a.id = :id')->setParameter('id', explode("-",$data['purchase_units'][0]['invoice_id'])[1])
                ->getQuery()
                ->getOneOrNullResult();
        $response_object=[];
        if($order){
            $status = $data['purchase_units'][0]['payments']['captures'][0]['status'];
            if($status=="COMPLETED" || $status == "APPROVED"){
                $order->setStatusPago(2); //COMPLETADA
                $order->setNotas($order->getNotas()." DATOS DE PAGO: ID PAYPAL: ".$data['purchase_units'][0]['payments']['captures'][0]['id']);
                //enviar mail
                $items_str='';
                $items = $em->getRepository('AppBundle:DespachoOrdenItem')
                ->createQueryBuilder('a')
                ->where('a.ordenDespacho = :orden')
                ->setParameter('orden', $order)
                ->getQuery()
                ->getResult();
                foreach ($items as $item) {
                    $precio = ($item->getBodega()=="DETAL")?$item->getProducto()->getProducto()->getPrecioDetal():$item->getProducto()->getProducto()->getPrecioMayorista();
                    $items_str = $items_str.'<tr>
                        <td style="text-align: center;border:1px solid black">'.$item->getProducto()->getProducto()->getNombre().'</td>
                        <td style="text-align: center;border:1px solid black">'.$item->getProducto()->getProducto()->getReferencia().'</td>
                        <td style="text-align: center;border:1px solid black">'.$item->getCantidad().'</td>
                        <td style="text-align: center;border:1px solid black">$'.$precio.'</td>
                        <td style="text-align: center;border:1px solid black">$'.($precio*$item->getCantidad()).'</td>
                    </tr>';
                }
                $url = "https://www.veroxcloset.com/send-email";//CAMBIAR EN PRODUCCION
                $client2 = new Client();
                $options = [
                    'form_params' => [
                        "json_string" => json_encode([
                            "email_to"=>$order->getClienteId()->getEmail(),
                            "email_cc"=>"",
                            "email_bcc"=>"",
                            "email_subject"=>"Confirmación de pedido VEROXCLOSET",
                            "email_body"=>'<h1>Hemos recibido el pago de tu pedido!</h1>
                            <span>A continuación los datos de la orden:</span><br><br>
                            <table style="text-align: center;border:1px solid black">
                                <tr>
                                    <th style="text-align: center;border:1px solid black">Item</th>
                                    <th style="text-align: center;border:1px solid black">Referencia</th>
                                    <th style="text-align: center;border:1px solid black">Cantidad</th>
                                    <th style="text-align: center;border:1px solid black">Precio</th>
                                    <th style="text-align: center;border:1px solid black">Subtotal</th>
                                </tr>'.$items_str.'
                            </table>
                            <br>
                            <h3>Envío: $'.$order->getCostoEnvio().'</h3>
                            <h3>Total: $'.$order->getTotal().'</h3>
                            <h3>Medio de Pago: PayPal</h3>
                            <h3>Id de Pago: hdhdjdk53513</h3>
                            <h3>Referencia del pedido: VRX-'.$order->getId().'</h3>
                            <h3>Anotaciones: '.$order->getNotas().'</h3><br>
                            <p>Te estaremos enviando información del despacho muy pronto.</p><br>
                            <h3>Atentamente,</h3>
                            <h4>Equipo VEROXCLOSET<h4>'
                        ])
                    ]
                ];
                $client2->post($url,$options);

            }
            else if($status == "VOIDED"){
                $order->setStatusPago(3); //ANULADA
            }
            $em->persist($order);
            $em->flush();
            $response_object = [
                "result"=>($order)?"completed":"failed",
                "data"=>[
                    "user_email"=>$order->getClienteId()->getEmail(),
                    "costo_envío"=>$order->getCostoEnvio(),
                    "total"=>$order->getTotal(),
                    "notas"=>$order->getNotas(),
                    "tipo_pago"=>"PayPal",
                ]
            ];
        }
        $response = new Response(json_encode($response_object));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    public function get_bonoAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $bono_code = $request->query->get('code');
        $bono = $em->getRepository(StoreBonos::class)->findOneBy(array('codigo' => $bono_code));
        if($bono){
            $data = [
                "id"=>$bono->getId(),
                "status"=>$bono->getEstatus(),
                "code"=>$bono->getCodigo(),
                "value"=>$bono->getValor(),
                "product_id"=>($bono->getProducto()==null) ? "Todos" : $bono->getProducto()->getId(),
                "user"=>($bono->getUsuario()==null) ? "Todos" :$bono->getUsuario() ->getEmail(),
                "category"=>($bono->getCategoria()==null) ? "Todos" : $bono->getCategoria()->getNombreEs(),
                "userType"=>($bono->getClienteTipo()==null) ? "Todos" : $bono->getClienteTipo(),
                "freeShipping"=>($bono->getFreeShipping()==null) ? false : (bool)$bono->getClienteTipo(),
                "expire"=>$bono->getFechaVencimiento()
            ];
            $response = new Response(json_encode($data));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        else{
            $response = new Response(json_encode([]));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
    public function test_mailAction(){
        $em = $this->getDoctrine()->getManager();
        //enviar mail
        $items_str='';
        $order = $em->getRepository(DespachoOrden::class)->createQueryBuilder('a')
                ->where('a.id = :id')->setParameter('id',7)
                ->getQuery()
                ->getOneOrNullResult();
        $items = $em->getRepository('AppBundle:DespachoOrdenItem')
        ->createQueryBuilder('a')
        ->where('a.ordenDespacho = :orden')
        ->setParameter('orden', $order)
        ->getQuery()
        ->getResult();
        foreach ($items as $item) {
            $precio = ($item->getBodega()=="DETAL")?$item->getProducto()->getProducto()->getPrecioDetal():$item->getProducto()->getProducto()->getPrecioMayorista();
            $items_str = $items_str.'<tr>
                <td style="text-align: center;border:1px solid black">'.$item->getProducto()->getProducto()->getNombre().'</td>
                <td style="text-align: center;border:1px solid black">'.$item->getCantidad().'</td>
                <td style="text-align: center;border:1px solid black">$'.$precio.'</td>
                <td style="text-align: center;border:1px solid black">$'.($precio*$item->getCantidad()).'</td>
            </tr>';
        }
        $url = "https://www.veroxcloset.com/send-email";//CAMBIAR EN PRODUCCION
        $client2 = new Client();
        $options = [
            'form_params' => [
                "json_string" => json_encode([
                    "email_to"=>$order->getClienteId()->getEmail(),
                    "email_cc"=>"",
                    "email_bcc"=>"",
                    "email_subject"=>"Confirmación de pedido VEROXCLOSET",
                    "email_body"=>'<h1>Hemos recibido el pago de tu pedido!</h1>
                    <span>A continuación los datos de la orden:</span><br><br>
                    <table style="text-align: center;border:1px solid black">
                        <tr>
                            <th style="text-align: center;border:1px solid black">Item</th>
                            <th style="text-align: center;border:1px solid black">Cantidad</th>
                            <th style="text-align: center;border:1px solid black">Precio</th>
                            <th style="text-align: center;border:1px solid black">Subtotal</th>
                        </tr>'.$items_str.'
                    </table>
                    <br>
                    <h3>Envío: $'.$order->getCostoEnvio().'</h3>
                    <h3>Total: $'.$order->getTotal().'</h3>
                    <h3>Medio de Pago: PayPal</h3>
                    <h3>Id de Pago: hdhdjdk53513</h3>
                    <h3>Referencia del pedido: VRX-'.$order->getId().'</h3><br>
                    <p>Te estaremos enviando información del despacho muy pronto.</p><br>
                    <h3>Atentamente,</h3>
                    <h4>Equipo VEROXCLOSET<h4>'
                ])
           ]]; 
        $response2 = $client2->post($url,$options);
        $server_answer = (string) $response2->getBody();
        $response = new Response($server_answer);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    public function get_order_dataAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $order_id = $request->query->get('order');
        $order = $em->getRepository(DespachoOrden::class)->find($order_id);
        $order_items = $em->getRepository('AppBundle:DespachoOrdenItem')
        ->createQueryBuilder('a')
        ->where('a.ordenDespacho = :orden')
        ->setParameter('orden', $order)
        ->getQuery()
        ->getResult();
        $data = [
            "user"=>$order->getClienteId()->getEmail(),
            "order_id"=>$order->getId(),
            "invoice"=>"VRX-".$order->getId(),
            "total"=>$order->getTotal(),
            "shipment"=>$order->getCostoEnvio(),
            "items"=>[]
        ];
        foreach ($order_items as $item) {
            array_push($data['items'],[
                "product_id"=>$item->getProducto()->getProducto()->getId(),
                "product_name"=>$item->getProducto()->getProducto()->getNombre(),
                "product_qty"=>$item->getCantidad(),
                "product_price"=>$item->getPrecio(),
                "product_color"=>$item->getColor(),
                "product_size"=>$item->getProducto()->getNombre(),
            ]);
        }
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    public function get_ordersAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $request->query->get('user');
        $data = [];
        $client = $em->getRepository(StoreUsuarios::class)->findOneBy(array('email' => $user));
        $orders = $em->getRepository('AppBundle:DespachoOrden')
        ->createQueryBuilder('a')
        ->where('a.clienteId = :client')
        ->setParameter('client', $client)
        ->getQuery()
        ->getResult();
        foreach ($orders as $order) {
            array_push($data,[
                "order_id"=>$order->getId(),
                "order_date"=>$order->getFechaCreacion(),
                "shipping_address"=>$order->getDireccionEnvio(),
                "payment_method"=>$order->getTipoPago(),
                "payment_status"=>$order->getStatusPago(),
                "order_status"=>$order->getStatusOrden(),
                "shipment_cost"=>$order->getCostoEnvio(),
                "total_value"=>$order->getTotal(),
                "order_notes"=>$order->getNotas(),
                "items"=>[]
            ]);
            $order_items = $em->getRepository('AppBundle:DespachoOrdenItem')
            ->createQueryBuilder('a')
            ->where('a.ordenDespacho = :orden')
            ->setParameter('orden', $order)
            ->getQuery()
            ->getResult();
            foreach ($order_items as $item) {
                foreach ($data as $key=>$order_data) {
                    if($order_data['order_id'] == $item->getOrdenDespacho()->getId()){
                        array_push($data[$key]['items'],[
                            "product_id"=>$item->getProducto()->getProducto()->getId(),
                            "product_name"=>$item->getProducto()->getProducto()->getNombre(),
                            "product_qty"=>$item->getCantidad(),
                            "product_price"=>$item->getPrecio(),
                            "product_color"=>$item->getColor(),
                            "product_size"=>$item->getProducto()->getNombre(),
                        ]);
                    }
                }
            }
        }
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    public function newFromStoreAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $data= json_decode($request->getContent(),true);
            $user = $em->getRepository(StoreUsuarios::class)->createQueryBuilder('a')
            ->where('a.email = :mail')->setParameter('mail', $data['email'])
            ->getQuery()
            ->getOneOrNullResult();
            if($user==null){
                $storeUsuario = new Storeusuarios();
                $storeUsuario->setEmail($data['email']);
                $storeUsuario->setNombre($data['nombre']);
                $storeUsuario->setApellidos($data['apellidos']);
                $storeUsuario->setTelefono($data['telefono']);
                $storeUsuario->setBirthday($data['birthday']);
                $storeUsuario->setWebId($data['webId']);
                $storeUsuario->setEstado('');
                $storeUsuario->setTipo('DETAL');
                $em->persist($storeUsuario);
                $em->flush($storeUsuario);
                
            }
            $response = new Response(json_encode(["response"=>"success"]));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (\Throwable $th) {
            $response = new Response(json_encode(["response"=>strval($th)]));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        
    }
    public function get_userAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:StoreUsuarios')
        ->createQueryBuilder('a')
        ->where('a.email = :mail')
        ->setParameter('mail', $request->query->get('email'))
        ->getQuery()
        ->getResult();
        $response = new Response(json_encode(["type"=>($user) ? $user[0]->getTipo() : "DETAL"]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    public function store_reporteAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $categorias = $em->getRepository('AppBundle:ProductoCategoria')
        ->createQueryBuilder('a')
        ->getQuery()
        ->getResult();
        $asesores_data = $em->getRepository('AppBundle:StoreUsuarios')
        ->createQueryBuilder('a')
        ->where('a.asesor != :asesor')
        ->setParameter('asesor', '')
        ->getQuery()
        ->getResult();

        $asesores = [];
        foreach ($asesores_data as $client) {
            array_push($asesores,$client->getAsesor());
        }
        return $this->render('storetienda/reporte.html.twig',[
            "categorias"=>$categorias,
            "asesores"=>array_unique($asesores),
        ]);        
    }
    public function get_reporteAction(Request $request)
    {
        try {
            $start_date = $request->request->get("start");
            $finish_date = $request->request->get("stop");
            $em = $this->getDoctrine()->getManager();
            
            $categorias = $em->getRepository('AppBundle:ProductoCategoria')
                ->createQueryBuilder('a')
                ->getQuery()
                ->getResult();

            $producto = $em->getRepository('AppBundle:Producto')
                ->createQueryBuilder('a')
                ->getQuery()
                ->getResult();

            $data = [];
            $reporte_total = 0;

            $orders = $em->getRepository('AppBundle:DespachoOrden')
                ->createQueryBuilder('a')
                ->where('a.statusPago IN (:estados)')
                ->setParameter('estados', [2, 3])
                ->andWhere('a.fechaPago >= :start OR a.fechaAbono1 >= :start OR a.fechaAbono2 >= :start') 
                ->setParameter('start', $start_date . " 00:00:00")
                ->andWhere('a.fechaPago <= :finish OR a.fechaAbono1 <= :finish OR a.fechaAbono2 <= :finish')
                ->setParameter('finish', $finish_date . " 23:59:00")
                ->getQuery()
                ->getResult();

            $firstTransactions = $em->getRepository('AppBundle:DespachoOrden')
                ->createQueryBuilder('a')
                ->select('IDENTITY(a.clienteId) as cliente_id')
                ->addSelect('MIN(COALESCE(a.fechaPago, a.fechaAbono1, a.fechaAbono2)) as primera_transaccion')
                ->where('a.statusPago IN (:estados)')
                ->setParameter('estados', [2, 3])
                ->andWhere('a.clienteId IS NOT NULL')
                ->andWhere('COALESCE(a.fechaPago, a.fechaAbono1, a.fechaAbono2) IS NOT NULL')
                ->groupBy('a.clienteId')
                ->getQuery()
                ->getResult();
            
            $clientesPrimeraTransaccion = [];
            $startDateTime = new \DateTime($start_date . " 00:00:00");
            $finishDateTime = new \DateTime($finish_date . " 23:59:59");
            
            foreach ($firstTransactions as $transaction) {
                $fechaPrimera = new \DateTime($transaction['primera_transaccion']);
                $clientesPrimeraTransaccion[$transaction['cliente_id']] = $fechaPrimera;
            }
                
            foreach ($orders as $order) {
                $clienteId = $order->getClienteId() ? $order->getClienteId()->getId() : null;
                $client_type = 'Nuevo';
                if ($clienteId && isset($clientesPrimeraTransaccion[$clienteId])) {
                    $fechaPrimera = $clientesPrimeraTransaccion[$clienteId];
                    if ($fechaPrimera < $startDateTime) {
                        $client_type = 'Antiguo';
                    }
                }

                $order_items = $em->getRepository('AppBundle:DespachoOrdenItem')
                    ->createQueryBuilder('a')
                    ->where('a.ordenDespacho = :orden')
                    ->setParameter('orden', $order)
                    ->getQuery()
                    ->getResult();
                    
                $items_array = [];
                foreach ($order_items as $item) {
                    array_push($items_array, [
                        "product_id" => $item->getProducto()->getProducto()->getId(),
                        "product_name" => $item->getProducto()->getProducto()->getNombre(),
                        "product_ref" => $item->getProducto()->getProducto()->getReferencia(),
                        "product_color" => $item->getColor(),
                        "product_talla" => $item->getProducto()->getNombre(),
                        "product_qty" => $item->getCantidad(),
                        "product_price" => $item->getPrecio(),
                        "Categoría" => $item->getProducto()->getProducto()->getCategoria()->getNOmbrees(),
                    ]);
                }
                
                $tieneAbonos = ($order->getAbono1() > 0 || $order->getAbono2() > 0);

                if ($order->getStatusPago() == 2 && $order->getFechaPago() && 
                    $order->getFechaPago() >= $startDateTime && 
                    $order->getFechaPago() <= $finishDateTime &&
                    !$tieneAbonos) {
                    
                    array_push($data, [
                        "order_id" => $order->getId() . "_COMPLETO",
                        "order_original_id" => $order->getId(),
                        "cliente_id" => $clienteId,
                        "cliente_nombre" => $order->getClienteId()->getNombre(),
                        "cliente_apellidos" => $order->getClienteId()->getApellidos(),
                        "cliente_email" => $order->getClienteId()->getEmail(),
                        "cliente_telefono" => $order->getClienteId()->getTelefono(),
                        "asesor" => $order->getClienteId()->getAsesor(),
                        "client_type" => $client_type,
                        "client_state" => $order->getClienteId()->getEstado(),
                        "order_date" => $order->getFechaPago()->format('Y-m-d'),
                        "order_price" => $order->getTotal(),
                        "monto_transaccion" => $order->getTotal(),
                        "tipo_transaccion" => 'Pago Completo',
                        "shipment_cost" => $order->getCostoEnvio(),
                        "estado_pago" => 'Pagado Completo',
                        "total_abonos" => 0,
                        "saldo_pendiente" => 0,
                        "items" => $items_array
                    ]);
                }

                if ($order->getFechaAbono1() && 
                    $order->getFechaAbono1() >= $startDateTime && 
                    $order->getFechaAbono1() <= $finishDateTime &&
                    $order->getAbono1() > 0) {
                    
                    array_push($data, [
                        "order_id" => $order->getId() . "_ABONO1",
                        "order_original_id" => $order->getId(),
                        "cliente_id" => $clienteId,
                        "cliente_nombre" => $order->getClienteId()->getNombre(),
                        "cliente_apellidos" => $order->getClienteId()->getApellidos(),
                        "cliente_email" => $order->getClienteId()->getEmail(),
                        "cliente_telefono" => $order->getClienteId()->getTelefono(),
                        "asesor" => $order->getClienteId()->getAsesor(),
                        "client_type" => $client_type,
                        "client_state" => $order->getClienteId()->getEstado(),
                        "order_date" => $order->getFechaAbono1()->format('Y-m-d'),
                        "order_price" => $order->getTotal(),
                        "monto_transaccion" => $order->getAbono1(),
                        "tipo_transaccion" => 'Abono 1',
                        "shipment_cost" => 0,
                        "estado_pago" => 'Abono Parcial',
                        "total_abonos" => $order->getTotalAbonos(),
                        "saldo_pendiente" => $order->getSaldoPendiente(),
                        "items" => $items_array
                    ]);
                }

                if ($order->getFechaAbono2() && 
                    $order->getFechaAbono2() >= $startDateTime && 
                    $order->getFechaAbono2() <= $finishDateTime &&
                    $order->getAbono2() > 0) {
                    
                    array_push($data, [
                        "order_id" => $order->getId() . "_ABONO2",
                        "order_original_id" => $order->getId(),
                        "cliente_id" => $clienteId,
                        "cliente_nombre" => $order->getClienteId()->getNombre(),
                        "cliente_apellidos" => $order->getClienteId()->getApellidos(),
                        "cliente_email" => $order->getClienteId()->getEmail(),
                        "cliente_telefono" => $order->getClienteId()->getTelefono(),
                        "asesor" => $order->getClienteId()->getAsesor(),
                        "client_type" => $client_type,
                        "client_state" => $order->getClienteId()->getEstado(),
                        "order_date" => $order->getFechaAbono2()->format('Y-m-d'),
                        "order_price" => $order->getTotal(),
                        "monto_transaccion" => $order->getAbono2(),
                        "tipo_transaccion" => 'Abono 2',
                        "shipment_cost" => 0,
                        "estado_pago" => 'Abono Parcial',
                        "total_abonos" => $order->getTotalAbonos(),
                        "saldo_pendiente" => $order->getSaldoPendiente(),
                        "items" => $items_array
                    ]);
                }

                if ($order->getStatusPago() == 2 && $order->getFechaPago() && 
                    $order->getFechaPago() >= $startDateTime && 
                    $order->getFechaPago() <= $finishDateTime &&
                    $tieneAbonos) {
                    
                    $montoRestante = $order->getTotal() - $order->getTotalAbonos();
                    if ($montoRestante > 0) {
                        array_push($data, [
                            "order_id" => $order->getId() . "_RESTANTE",
                            "order_original_id" => $order->getId(),
                            "cliente_id" => $clienteId,
                            "cliente_nombre" => $order->getClienteId()->getNombre(),
                            "cliente_apellidos" => $order->getClienteId()->getApellidos(),
                            "cliente_email" => $order->getClienteId()->getEmail(),
                            "cliente_telefono" => $order->getClienteId()->getTelefono(),
                            "asesor" => $order->getClienteId()->getAsesor(),
                            "client_type" => $client_type,
                            "client_state" => $order->getClienteId()->getEstado(),
                            "order_date" => $order->getFechaPago()->format('Y-m-d'),
                            "order_price" => $order->getTotal(),
                            "monto_transaccion" => $montoRestante,
                            "tipo_transaccion" => 'Pago Restante',
                            "shipment_cost" => $order->getCostoEnvio(),
                            "estado_pago" => 'Completado',
                            "total_abonos" => $order->getTotalAbonos(),
                            "saldo_pendiente" => 0,
                            "items" => $items_array
                        ]);
                    }
                }
            }
            
            $reporte_tipo = $request->request->get("reporte_tipo");
            $reporte_tag = $request->request->get("reporte_tag");
            
            $reporte = [];
            $pqty = [];
            
            foreach ($data as $key => $value) {
                if ($reporte_tipo == "Total") {
                    array_push($reporte, [
                        'id' => $value['order_id'],
                        'original_id' => $value['order_original_id'],
                        'cliente_id' => $value['cliente_id'],
                        'cliente_nombre' => $value['cliente_nombre'],
                        'cliente_apellidos' => $value['cliente_apellidos'],
                        'cliente_email' => $value['cliente_email'],
                        'cliente_telefono' => $value['cliente_telefono'],
                        'date' => $value['order_date'],
                        'price' => $value['order_price'],
                        'monto_transaccion' => $value['monto_transaccion'],
                        'tipo_transaccion' => $value['tipo_transaccion'],
                        'estado_pago' => $value['estado_pago'],
                        'total_abonos' => $value['total_abonos'],
                        'saldo_pendiente' => $value['saldo_pendiente'],
                        'items' => $value['items'],
                        'shipment_cost' => $value['shipment_cost'],
                        'asesor' => $value['asesor'],
                        'client_type' => $value['client_type'],
                        'client_state' => $value['client_state']
                    ]);
                } elseif ($reporte_tipo == "Categoria") {
                    $items2 = [];
                    foreach ($value['items'] as $key2 => $value2) {
                        if ($reporte_tag == $value2['Categoría']) {
                            array_push($items2, $value2);
                        }
                    }
                    if (count($items2) > 0) {
                        array_push($reporte, [
                            'id' => $value['order_id'],
                            'original_id' => $value['order_original_id'],
                            'date' => $value['order_date'],
                            'monto_transaccion' => $value['monto_transaccion'],
                            'tipo_transaccion' => $value['tipo_transaccion'],
                            'estado_pago' => $value['estado_pago'],
                            "items" => $items2,
                            'shipment_cost' => $value['shipment_cost'],
                        ]);
                    }
                } elseif ($reporte_tipo == "Top") {
                    foreach ($value['items'] as $key2 => $value2) {
                        if (!isset($pqty[$value2['product_ref']])) {
                            $pqty[$value2['product_ref']] = [
                                "cantidad" => 0,
                                "talla" => $value2['product_talla'],
                                "color" => $value2['product_color'],
                                "referencia" => $value2['product_ref'],
                                "nombre" => $value2['product_name'],
                                "total" => 0
                            ];
                        }
                    }
                    foreach ($value['items'] as $key2 => $value2) {
                        $factorProporcional = $value['monto_transaccion'] / $value['order_price'];
                        
                        $pqty[$value2['product_ref']]['cantidad'] += ($value2['product_qty'] * $factorProporcional);
                        $pqty[$value2['product_ref']]['total'] += ($value2['product_price'] * $value2['product_qty'] * $factorProporcional);
                    }
                } elseif ($reporte_tipo == "Asesor") {
                    if ($value['asesor'] == $reporte_tag) {
                        array_push($reporte, [
                            'id' => $value['order_id'],
                            'original_id' => $value['order_original_id'],
                            'date' => $value['order_date'],
                            'price' => $value['order_price'],
                            'monto_transaccion' => $value['monto_transaccion'],
                            'tipo_transaccion' => $value['tipo_transaccion'],
                            'estado_pago' => $value['estado_pago'],
                            'asesor' => $value['asesor'],
                            'shipment_cost' => $value['shipment_cost'],
                            'items' => $value['items']
                        ]);
                    }
                }
            }
            
            if ($reporte_tipo == "Top") {
                foreach ($pqty as $key3 => $value3) {
                    array_push($reporte, [
                        'id' => '',
                        'date' => '',
                        'total' => $value3['total'],
                        'cantidad' => $value3['cantidad'],
                        'nombre' => $value3['nombre'],
                        'talla' => $value3['talla'],
                        'color' => $value3['color'],
                    ]);
                }
            }
            
            $response = new Response(json_encode($reporte));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (\Throwable $th) {
            $response = new Response($th->getMessage());
            return $response;
        }
    }
}
?>