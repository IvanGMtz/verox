<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProductoColor;
use AppBundle\Entity\Producto;
use AppBundle\Entity\ProductoInventario;
use AppBundle\Entity\ProductoInventarioMovimiento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use AppBundle\Entity\ProductoTalla;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Productoinventario controller.
 *
 */
class ProductoInventarioController extends Controller
{
    /**
     * Lists all productoInventario entities.
     *
     */
public function indexAction(Request $request, PaginatorInterface $paginator) {
    $em = $this->getDoctrine()->getManager();
    $q = $request->request->get('q', $this->get('session')->get('q.ProductoInventario', []));
    
    $page = $request->isMethod('POST') ? 1 : $request->query->getInt('page', 1);
    
    $productoInventariosQ = $em->getRepository('AppBundle:ProductoInventario')
        ->createQueryBuilder('a')
        ->join('a.producto', 'p')
        ->join('p.producto', 'p2');

    if (!empty(array_filter($q))) {
        $this->get('session')->set('q.ProductoInventario', $q);
        $conditions = [];
        $params = [];

        // 1. Manejo de rangos de cantidad mayorista
        if (isset($q['qtyMayoristaMin']) && $q['qtyMayoristaMin'] !== '') {
            $conditions[] = 'a.qtyActualMayorista >= :qtyMin';
            $params['qtyMin'] = (int)$q['qtyMayoristaMin'];
        }
        if (isset($q['qtyMayoristaMax']) && $q['qtyMayoristaMax'] !== '') {
            $conditions[] = 'a.qtyActualMayorista <= :qtyMax';
            $params['qtyMax'] = (int)$q['qtyMayoristaMax'];
        }

        // 2. Filtros de texto (referencia y nombre)
        foreach (['referencia', 'nombre'] as $field) {
            if (!empty($q[$field])) {
                $conditions[] = "p2.$field LIKE :$field";
                $params[$field] = '%'.$q[$field].'%';
            }
        }

        // 3. Combinar todas las condiciones
        if (!empty($conditions)) {
            $productoInventariosQ->where(implode(' AND ', $conditions));
            foreach ($params as $key => $value) {
                $productoInventariosQ->setParameter($key, $value);
            }
        }
    }

    $pagination = $paginator->paginate(
        $productoInventariosQ->getQuery(),
        $page,
        50
    );

    return $this->render('productoinventario/index.html.twig', [
        'productoInventarios' => $pagination->getItems(),
        'q' => $q,
        'pagination' => $pagination
    ]);
    }

    /**
     * Creates a new productoInventario entity.
     *
     */
    public function newAction(Request $request)
    {
        $productoInventario = new Productoinventario();
        $form = $this->createForm('AppBundle\Form\ProductoInventarioType', $productoInventario);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();
        $data = json_decode($request->request->get('products_data'),true);
        if ($data && $user) {
            foreach ($data as $register) {
                $producto = $this->getDoctrine()->getRepository(ProductoTalla::class)->find($register["producto_talla_id"]);
                $color = $this->getDoctrine()->getRepository(ProductoColor::class)->find($register["producto_color"]);
                $existencia = $this->getDoctrine()->getRepository(ProductoInventario::class)->findOneBy(array('producto' => $producto,'color' => $color));
                if($existencia == null){
                    $productoInventario = new Productoinventario();
                    $productoInventario->setProducto($producto);
                    $productoInventario->setColor($color);
                    if($register["bodega"]=="detal"){
                        $productoInventario->setIngresoDetal($register["cantidad"]);
                        $productoInventario->setEgresoDetal(0);
                        $productoInventario->setQtyActualDetal($register["cantidad"]);
                        $productoInventario->setUltimoIngresoD($fecha);
                        $productoInventario->setIngresoMayorista(0);
                        $productoInventario->setEgresoMayorista(0);
                        $productoInventario->setQtyActualMayorista(0);
                        $em->persist($productoInventario);
                        $em->flush();
                    }
                    else {
                        $productoInventario->setIngresoDetal(0);
                        $productoInventario->setEgresoDetal(0);
                        $productoInventario->setQtyActualDetal(0);
                        $productoInventario->setIngresoMayorista($register["cantidad"]);
                        $productoInventario->setEgresoMayorista(0);
                        $productoInventario->setQtyActualMayorista($register["cantidad"]);
                        $productoInventario->setUltimoIngresoM($fecha);
                        $em->persist($productoInventario);
                        $em->flush();
                    }
                }
                else{
                    if($register["bodega"]=="detal"){
                        $existencia->setIngresoDetal($existencia->getIngresoDetal() + $register["cantidad"]);
                        $existencia->setQtyActualDetal($existencia->getQtyActualDetal() + $register["cantidad"]);
                        $existencia->setUltimoIngresoD($fecha);
                        $em->persist($existencia);
                        $em->flush();
                    }
                    else{
                        $existencia->setIngresoMayorista($existencia->getIngresoMayorista() + $register["cantidad"]);
                        $existencia->setQtyActualMayorista($existencia->getQtyActualMayorista() + $register["cantidad"]);
                        $existencia->setUltimoIngresoM($fecha);
                        $existencia->setQtyActualDetal(2);
                        $em->persist($existencia);
                        $em->flush();
                    }
                }
                //guardar movimiento
                $inventarioMovimiento = new ProductoInventarioMovimiento();
                $inventarioMovimiento->setProducto($producto->getProducto()->getNombre()." Talla: ".$producto->getNombre());
                $inventarioMovimiento->setColor($color->getNombre());
                $inventarioMovimiento->setMovimiento("Ingreso");
                $inventarioMovimiento->setCantidad($register["cantidad"]);
                $inventarioMovimiento->setBodega($register["bodega"]);
                $inventarioMovimiento->setInformacion("Ingreso manual de inventario");
                $inventarioMovimiento->setUsuario($user->getName());
                $inventarioMovimiento->setFecha($fecha);
                $em->persist($inventarioMovimiento);
                $em->flush();
            }
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productoinventario_index');
        }
        return $this->render('productoinventario/new.html.twig', array(
            'productoInventario' => $productoInventario,
            'form' => $form->createView(),
        ));
    }

    public function outAction(Request $request)
    {
        $productoInventario = new Productoinventario();
        $form = $this->createForm('AppBundle\Form\ProductoInventarioType', $productoInventario);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();
        $data = json_decode($request->request->get('products_data'),true);
        if ($data && $user) {
            foreach ($data as $register) {
                $producto = $this->getDoctrine()->getRepository(ProductoTalla::class)->find($register["producto_talla_id"]);
                $color = $this->getDoctrine()->getRepository(ProductoColor::class)->find($register["producto_color"]);
                $existencia = $this->getDoctrine()->getRepository(ProductoInventario::class)->findOneBy(array('producto' => $producto,'color' => $color));
                if($existencia == null){
                    $this->addFlash(
                        'error',
                        'El item '.$producto->getProducto()->getNombre().' Talla: '.$producto->getNombre().' no tiene unidades en el inventario para el egreso'
                    );
                    return $this->redirectToRoute('productoinventario_index');
                }
                else{
                    if($register["bodega"]=="detal"){
                        if($existencia->getQtyActualDetal() < $register["cantidad"]){
                            $this->addFlash(
                                'error',
                                'El item '.$producto->getProducto()->getNombre().' Talla: '.$producto->getNombre().' no tiene unidades en el inventario para el egreso'
                            );
                            return $this->redirectToRoute('productoinventario_index');
                        }
                        $existencia->setEgresoDetal($existencia->getEgresoDetal() + $register["cantidad"]);
                        $existencia->setQtyActualDetal($existencia->getQtyActualDetal() - $register["cantidad"]);
                        $existencia->setUltimoEgresoD($fecha);
                        $em->persist($existencia);
                        $em->flush();
                    }
                    else{
                        if($existencia->getQtyActualMayorista() < $register["cantidad"]){
                            $this->addFlash(
                                'error',
                                'El item '.$producto->getProducto()->getNombre().' Talla: '.$producto->getNombre().' no tiene unidades en el inventario para el egreso'
                            );
                            return $this->redirectToRoute('productoinventario_index');
                        }
                        if($existencia->getQtyActualMayorista() - $register["cantidad"] <= 0){
                            $existencia->setQtyActualDetal(0);
                        }
                        $existencia->setEgresoMayorista($existencia->getEgresoMayorista() + $register["cantidad"]);
                        $existencia->setQtyActualMayorista($existencia->getQtyActualMayorista() - $register["cantidad"]);
                        $existencia->setUltimoEgresoM($fecha);
                        $em->persist($existencia);
                        $em->flush();
                    }
                }
                //guardar movimiento
                $inventarioMovimiento = new ProductoInventarioMovimiento();
                $inventarioMovimiento->setProducto($producto->getProducto()->getNombre()." Talla: ".$producto->getNombre());
                $inventarioMovimiento->setColor($color->getNombre());
                $inventarioMovimiento->setMovimiento("Egreso");
                $inventarioMovimiento->setCantidad($register["cantidad"]);
                $inventarioMovimiento->setBodega($register["bodega"]);
                $inventarioMovimiento->setInformacion("Egreso manual de inventario");
                $inventarioMovimiento->setUsuario($user->getName());
                $inventarioMovimiento->setFecha($fecha);
                $em->persist($inventarioMovimiento);
                $em->flush();
            }
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productoinventario_index');
        }
        return $this->render('productoinventario/out.html.twig', array(
            'productoInventario' => $productoInventario,
            'form' => $form->createView(),
        ));
    }
    public function showAction(ProductoInventario $productoInventario)
    {
        $deleteForm = $this->createDeleteForm($productoInventario);

        return $this->render('productoinventario/show.html.twig', array(
            'productoInventario' => $productoInventario,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing productoInventario entity.
     *
     */
    public function editAction(Request $request, ProductoInventario $productoInventario)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($productoInventario);
        $editForm = $this->createForm('AppBundle\Form\ProductoInventarioType', $productoInventario);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('productoinventario_index');
        }

        return $this->render('productoinventario/edit.html.twig', array(
            'productoInventario' => $productoInventario,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a productoInventario entity.
     *
     */
    public function deleteAction(Request $request, ProductoInventario $productoInventario)
    {
        $form = $this->createDeleteForm($productoInventario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productoInventario);
            $em->flush($productoInventario);
        }

        return $this->redirectToRoute('productoinventario_index');
    }

    /**
     * Creates a form to delete a productoInventario entity.
     *
     * @param ProductoInventario $productoInventario The productoInventario entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProductoInventario $productoInventario)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productoinventario_delete', array('id' => $productoInventario->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProductoInventario $productoInventario)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($productoInventario);
        $em->flush($productoInventario);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('productoinventario_index');
    }
    public function reportAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('AppBundle:ProductoInventario')->findAll();
        // Solicita el servicio de excel
       $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

       $phpExcelObject->getProperties()->setCreator("Admin")
           ->setLastModifiedBy("Admin")
           ->setTitle("Office 2005 XLSX Test Document")
           ->setSubject("Office 2005 XLSX Test Document")
           ->setDescription("Reporte de Inventario de Productos")
           ->setKeywords("office 2005 openxml php")
           ->setCategory("Inventario de Productos");
        $row = 2;
        foreach ($data as $item) {
            $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', "REFERENCIA")
            ->setCellValue('A'.$row, $item->getProducto()->getProducto()->getReferencia())
            ->setCellValue('B1', "NOMBRE")
            ->setCellValue('B'.$row, $item->getProducto()->getProducto()->getNombre())
            ->setCellValue('C1', "TALLA")
            ->setCellValue('C'.$row, $item->getProducto()->getNombre())
            ->setCellValue('D1', "COLOR")
            ->setCellValue('D'.$row, $item->getColor()->getNombre())
            ->setCellValue('E1', "QTY DETAL")
            ->setCellValue('E'.$row, (string)$item->getQtyActualDetal())
            ->setCellValue('F1', "ULTIMO INGRESO DETAL")
            ->setCellValue('F'.$row, $item->getUltimoIngresoD())
            ->setCellValue('G1', "ULTIMO EGRESO DETAL")
            ->setCellValue('G'.$row, $item->getUltimoEgresoD())
            ->setCellValue('H1', "QTY MAYORISTAS")
            ->setCellValue('H'.$row, (string)$item->getQtyActualMayorista())
            ->setCellValue('I1', "ULTIMO INGRESO MAYORISTAS")
            ->setCellValue('I'.$row, $item->getUltimoIngresoM())
            ->setCellValue('J1', "ULTIMO EGRESO MAYORISTAS")
            ->setCellValue('J'.$row, $item->getUltimoEgresoM())
            ;
            $row++;
        }
       
       $phpExcelObject->getActiveSheet()->setTitle('Simple');
       // Define el indice de página al número 1, para abrir esa página al abrir el archivo
       $phpExcelObject->setActiveSheetIndex(0);

        // Crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        // Envia la respuesta del controlador
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // Agrega los headers requeridos
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'costo_inventario.xlsx'
        );

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;  
    }
    public function importexcelAction(Request $request){
        set_time_limit(0);
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $data = json_decode($request->request->get('products_data'),true);
        $fecha = new \DateTime();
        if ($data && $user){
            $classMetaData = $em->getClassMetadata(ProductoInventario::class);
            $connection = $em->getConnection();
            $dbPlatform = $connection->getDatabasePlatform();
            $connection->beginTransaction();
            try {
                $connection->query('SET FOREIGN_KEY_CHECKS=0');
                $q = $dbPlatform->getTruncateTableSql($classMetaData->getTableName());
                $connection->executeUpdate($q);
                $connection->query('SET FOREIGN_KEY_CHECKS=1');
                $connection->commit();
            }
            catch (\Exception $e) {
                $connection->rollback();
                $this->addFlash(
                    'error',
                    'Error de base de datos'
                );
                return $this->redirectToRoute('productoinventario_importexcel');
            }
            $classMetaData = $em->getClassMetadata(ProductoInventarioMovimiento::class);
            $connection = $em->getConnection();
            $dbPlatform = $connection->getDatabasePlatform();
            $connection->beginTransaction();
            try {
                $connection->query('SET FOREIGN_KEY_CHECKS=0');
                $q = $dbPlatform->getTruncateTableSql($classMetaData->getTableName());
                $connection->executeUpdate($q);
                $connection->query('SET FOREIGN_KEY_CHECKS=1');
                $connection->commit();
            }
            catch (\Exception $e) {
                $connection->rollback();
                $this->addFlash(
                    'error',
                    'Error de base de datos'
                );
                return $this->redirectToRoute('productoinventario_importexcel');
            }
            foreach ($data as $index=>$register){
                if($index>0){
                    $producto_base =  $this->getDoctrine()->getRepository(Producto::class)->findOneBy(array('referencia' => $register[0]));
                    if($producto_base){
                        $producto_talla = $this->getDoctrine()->getRepository(ProductoTalla::class)->findOneBy(array('producto' => $producto_base->getId(),'nombre' => $register[2]));
                        if($producto_talla){
                            $producto_color = $this->getDoctrine()->getRepository(ProductoColor::class)->findOneBy(array('producto' => $producto_base->getId(),'nombre' => $register[3]));
                            if($producto_color){
                                $productoInventario = new Productoinventario();
                                $productoInventario->setProducto($producto_talla);
                                $productoInventario->setColor($producto_color);
                                $productoInventario->setIngresoDetal(($register[4]>0)?2:0);
                                $productoInventario->setEgresoDetal(0);
                                $productoInventario->setQtyActualDetal(($register[4]>0)?2:0);
                                $productoInventario->setUltimoIngresoD($fecha);
                                $productoInventario->setIngresoMayorista($register[4]);
                                $productoInventario->setEgresoMayorista(0);
                                $productoInventario->setQtyActualMayorista($register[4]);
                                $productoInventario->setUltimoIngresoM($fecha);
                                $em->persist($productoInventario);
                                $em->flush();

                                $productoInventarioMovimiento = new ProductoInventarioMovimiento();
                                $productoInventarioMovimiento->setProducto($register[0].' Talla: '.$register[2]);
                                $productoInventarioMovimiento->setColor($register[3]);
                                $productoInventarioMovimiento->setMovimiento('Ingreso');
                                $productoInventarioMovimiento->setCantidad($register[4]);
                                $productoInventarioMovimiento->setBodega('mayorista');
                                $productoInventarioMovimiento->setInformacion('Ingreso por excel (cantidad en detal = '.(($register[4]>0)?2:0).' por defecto)');
                                $productoInventarioMovimiento->setUsuario($user->getName());
                                $productoInventarioMovimiento->setFecha($fecha);
                                $em->persist($productoInventarioMovimiento);
                                $em->flush();
                            }
                            else{
                                $this->addFlash(
                                    'error',
                                    'No se existe lel color '.$register[3].' del producto con referencia '.$register[0]
                                );
                                return $this->redirectToRoute('productoinventario_importexcel');
                            }
                        }
                        else{
                            $this->addFlash(
                                'error',
                                'No se existe la talla '.$register[2].' del producto con referencia '.$register[0]
                            );
                            return $this->redirectToRoute('productoinventario_importexcel');
                        }
                    }
                    else{
                        $this->addFlash(
                            'error',
                            'No se existe el producto con referencia '.$register[0]
                        );
                        return $this->redirectToRoute('productoinventario_importexcel');
                    }
                }
            }
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productoinventario_index');
        }
        return $this->render('productoinventario/importexcel.html.twig');
    }
    public function importexcelnewAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $data = json_decode($request->request->get('products_data'),true);
        $fecha = new \DateTime();
        if ($data && $user){
            foreach ($data as $index=>$register){
                if($index>0){
                    $producto_base =  $this->getDoctrine()->getRepository(Producto::class)->findOneBy(array('referencia' => $register[0]));
                    if($producto_base){
                        $producto_talla = $this->getDoctrine()->getRepository(ProductoTalla::class)->findOneBy(array('producto' => $producto_base->getId(),'nombre' => $register[2]));
                        if($producto_talla){
                            $producto_color = $this->getDoctrine()->getRepository(ProductoColor::class)->findOneBy(array('producto' => $producto_base->getId(),'nombre' => $register[3]));
                            if($producto_color){
                                $alreadyExists = $this->getDoctrine()->getRepository(ProductoInventario::class)->findOneBy(array('producto' => $producto_talla,'color' => $producto_color));
                                if($alreadyExists){
                                    $this->addFlash(
                                        'error',
                                        'El producto '.$register[1].' talla: '.$register[2].' color: '.$register[3].' ya existe en el inventario'
                                    );
                                    return $this->redirectToRoute('productoinventario_importexcelnew');
                                }
                                $productoInventario = new Productoinventario();
                                $productoInventario->setProducto($producto_talla);
                                $productoInventario->setColor($producto_color);
                                $productoInventario->setIngresoDetal(($register[4]>0)?2:0);
                                $productoInventario->setEgresoDetal(0);
                                $productoInventario->setQtyActualDetal(($register[4]>0)?2:0);
                                $productoInventario->setUltimoIngresoD($fecha);
                                $productoInventario->setIngresoMayorista($register[4]);
                                $productoInventario->setEgresoMayorista(0);
                                $productoInventario->setQtyActualMayorista($register[4]);
                                $productoInventario->setUltimoIngresoM($fecha);
                                $em->persist($productoInventario);
                                $em->flush();

                                $productoInventarioMovimiento = new ProductoInventarioMovimiento();
                                $productoInventarioMovimiento->setProducto($register[0].' Talla: '.$register[2]);
                                $productoInventarioMovimiento->setColor($register[3]);
                                $productoInventarioMovimiento->setMovimiento('Ingreso');
                                $productoInventarioMovimiento->setCantidad($register[4]);
                                $productoInventarioMovimiento->setBodega('mayorista');
                                $productoInventarioMovimiento->setInformacion('Ingreso por excel (cantidad en detal = '.(($register[4]>0)?2:0).' por defecto)');
                                $productoInventarioMovimiento->setUsuario($user->getName());
                                $productoInventarioMovimiento->setFecha($fecha);
                                $em->persist($productoInventarioMovimiento);
                                $em->flush();
                            }
                            else{
                                $this->addFlash(
                                    'error',
                                    'No se existe lel color '.$register[3].' del producto con referencia '.$register[0]
                                );
                                return $this->redirectToRoute('productoinventario_importexcelnew');
                            }
                        }
                        else{
                            $this->addFlash(
                                'error',
                                'No se existe la talla '.$register[2].' del producto con referencia '.$register[0]
                            );
                            return $this->redirectToRoute('productoinventario_importexcelnew');
                        }
                    }
                    else{
                        $this->addFlash(
                            'error',
                            'No se existe el producto con referencia '.$register[0]
                        );
                        return $this->redirectToRoute('productoinventario_importexcelnew');
                    }
                }
            }
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productoinventario_index');
        }
        return $this->render('productoinventario/importexcelnew.html.twig');
    }
    public function importexcelreplaceAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $data = json_decode($request->request->get('products_data'),true);
        $fecha = new \DateTime();
        if ($data && $user){
            foreach ($data as $index=>$register){
                if($index>0){
                    $producto_base =  $this->getDoctrine()->getRepository(Producto::class)->findOneBy(array('referencia' => $register[0]));
                    if($producto_base){
                        $producto_talla = $this->getDoctrine()->getRepository(ProductoTalla::class)->findOneBy(array('producto' => $producto_base->getId(),'nombre' => $register[2]));
                        if($producto_talla){
                            $producto_color = $this->getDoctrine()->getRepository(ProductoColor::class)->findOneBy(array('producto' => $producto_base->getId(),'nombre' => $register[3]));
                            if($producto_color){
                                $alreadyExists = $this->getDoctrine()->getRepository(ProductoInventario::class)->findOneBy(array('producto' => $producto_talla,'color' => $producto_color));
                                if($alreadyExists){
                                    $alreadyExists->setProducto($producto_talla);
                                    $alreadyExists->setColor($producto_color);
                                    $alreadyExists->setIngresoDetal(($register[4]>0)?2:0);
                                    $alreadyExists->setEgresoDetal(0);
                                    $alreadyExists->setQtyActualDetal(($register[4]>0)?2:0);
                                    $alreadyExists->setUltimoIngresoD($fecha);
                                    $alreadyExists->setIngresoMayorista($register[4]);
                                    $alreadyExists->setEgresoMayorista(0);
                                    $alreadyExists->setQtyActualMayorista($register[4]);
                                    $alreadyExists->setUltimoIngresoM($fecha);
                                    $em->persist($alreadyExists);
                                    $em->flush();

                                    $productoInventarioMovimiento = new ProductoInventarioMovimiento();
                                    $productoInventarioMovimiento->setProducto($register[0].' Talla: '.$register[2]);
                                    $productoInventarioMovimiento->setColor($register[3]);
                                    $productoInventarioMovimiento->setMovimiento('Ingreso');
                                    $productoInventarioMovimiento->setCantidad($register[4]);
                                    $productoInventarioMovimiento->setBodega('mayorista');
                                    $productoInventarioMovimiento->setInformacion('Ingreso por excel (cantidad en detal = '.(($register[4]>0)?2:0).' por defecto)');
                                    $productoInventarioMovimiento->setUsuario($user->getName());
                                    $productoInventarioMovimiento->setFecha($fecha);
                                    $em->persist($productoInventarioMovimiento);
                                    $em->flush();
                                }
                                else{
                                    $this->addFlash(
                                        'error',
                                        'El producto '.$register[1].' talla: '.$register[2].' color: '.$register[3].' no existe en el inventario'
                                    );
                                    return $this->redirectToRoute('productoinventario_importexcelreplace');
                                }
                            }
                            else{
                                $this->addFlash(
                                    'error',
                                    'No se existe lel color '.$register[3].' del producto con referencia '.$register[0]
                                );
                                return $this->redirectToRoute('productoinventario_importexcelreplace');
                            }
                        }
                        else{
                            $this->addFlash(
                                'error',
                                'No se existe la talla '.$register[2].' del producto con referencia '.$register[0]
                            );
                            return $this->redirectToRoute('productoinventario_importexcelreplace');
                        }
                    }
                    else{
                        $this->addFlash(
                            'error',
                            'No se existe el producto con referencia '.$register[0]
                        );
                        return $this->redirectToRoute('productoinventario_importexcelreplace');
                    }
                }
            }
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productoinventario_index');
        }
        return $this->render('productoinventario/importexcelreplace.html.twig');
    }
}
