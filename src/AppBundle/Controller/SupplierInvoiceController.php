<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SupplierInvoice;
use AppBundle\Entity\SupplierInvoiceItem;
use AppBundle\Entity\ProductoInventario;
use AppBundle\Entity\ProductoInventarioMovimiento;
use AppBundle\Entity\ProductoTalla;
use AppBundle\Entity\ProductoColor;
use AppBundle\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * SupplierInvoice controller.
 * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN_VENTAS')")
 */
class SupplierInvoiceController extends Controller
{
    /**
     * Lists all supplierInvoice entities.
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.SupplierInvoice'));
        if ($request->isMethod('POST')) {
            $page = 1;
        } else {
            $page = $request->query->getInt('page', 1);
        }

        $supplierInvoicesQ = $em->getRepository('AppBundle:SupplierInvoice')->createQueryBuilder('a')
            ->join('a.proveedor', 'p');

        if ($q && $q != '') {
            $this->get('session')->set('q.SupplierInvoice', $q);
            $qcount = 0;
            foreach ($q as $field => $value) {
                if ($value) {
                    if ($qcount == 0) {
                        if ($field == "proveedor") {
                            $supplierInvoicesQ->where('p.nombre LIKE :' . $field)->setParameter($field, '%' . $value . '%');
                        } else {
                            $supplierInvoicesQ->where('a.' . $field . ' LIKE :' . $field)->setParameter($field, '%' . $value . '%');
                        }
                    } else {
                        if ($field == "proveedor") {
                            $supplierInvoicesQ->andWhere('p.nombre LIKE :' . $field)->setParameter($field, '%' . $value . '%');
                        } else {
                            $supplierInvoicesQ->andWhere('a.' . $field . ' LIKE :' . $field)->setParameter($field, '%' . $value . '%');
                        }
                    }
                    $qcount++;
                }
            }
        }

        $supplierInvoicesQ->orderBy('a.fechaCreacion', 'DESC');
        $query = $supplierInvoicesQ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $page,
            50
        );

        $supplierInvoices = $pagination->getItems();

        return $this->render('supplierinvoice/index.html.twig', array(
            'supplierInvoices' => $supplierInvoices,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Confirms a supplier invoice and updates product inventory
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN_VENTAS')")
     */
    public function confirmAction(SupplierInvoice $supplierInvoice)
    {
        if ($supplierInvoice->getInventoryUpdated()) {
            $this->addFlash('error', 'La factura ya ha sido confirmada y actualizado el inventario');
            return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
        }

        if ($supplierInvoice->getEstado() == SupplierInvoice::STATUS_CANCELLED) {
            $this->addFlash('error', 'No se puede confirmar una factura cancelada');
            return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        try {
            $em->beginTransaction();

            // Actualizar inventario de productos por cada item
            foreach ($supplierInvoice->getItems() as $item) {
                $this->updateProductInventoryForItem($item, $user, $fecha, $supplierInvoice);
            }

            // Actualizar estado de la factura
            $supplierInvoice->setEstado(SupplierInvoice::STATUS_CONFIRMED);
            $supplierInvoice->setInventoryUpdated(true);
            $supplierInvoice->setUsuarioActualizacion($user);
            $supplierInvoice->setFechaActualizacion($fecha);

            $em->flush();
            $em->commit();

            $this->addFlash('success', 'Factura confirmada e inventario de productos actualizado correctamente');
        } catch (\Exception $e) {
            $em->rollback();
            $this->addFlash('error', 'Error al confirmar la factura: ' . $e->getMessage());
        }

        return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
    }

    /**
     * Cancels a supplier invoice
     */
    public function cancelAction(SupplierInvoice $supplierInvoice)
    {
        if ($supplierInvoice->getInventoryUpdated()) {
            $this->addFlash('error', 'No se puede cancelar una factura que ya ha actualizado el inventario');
            return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        $supplierInvoice->setEstado(SupplierInvoice::STATUS_CANCELLED);
        $supplierInvoice->setUsuarioActualizacion($user);
        $supplierInvoice->setFechaActualizacion($fecha);

        $em->flush();

        $this->addFlash('success', 'Factura cancelada correctamente');
        return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
    }

    /**
     * Deletes a supplierInvoice entity.
     */
    public function deleteAction(Request $request, SupplierInvoice $supplierInvoice)
    {
        if ($supplierInvoice->getInventoryUpdated()) {
            $this->addFlash('error', 'No se puede eliminar una factura que ya ha actualizado el inventario');
            return $this->redirectToRoute('supplierinvoice_index');
        }

        $form = $this->createDeleteForm($supplierInvoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Eliminar archivo adjunto si existe
            if ($supplierInvoice->getAttachmentPath()) {
                $filePath = $this->getParameter('kernel.root_dir') . '/../web/' . $supplierInvoice->getAttachmentPath();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $em->remove($supplierInvoice);
            $em->flush();

            $this->addFlash('success', 'Factura eliminada correctamente');
        }

        return $this->redirectToRoute('supplierinvoice_index');
    }

    /**
     * AJAX search for products (instead of materials)
     */
    public function searchProductsAction(Request $request)
    {
        $q = $request->query->get('q', '');
        
        if (strlen($q) < 2) {
            return new JsonResponse([]);
        }

        $em = $this->getDoctrine()->getManager();
        
        // Buscar productos por referencia o nombre
        $productos = $em->getRepository('AppBundle:Producto')
            ->createQueryBuilder('p')
            ->where('p.referencia LIKE :search OR p.nombre LIKE :search')
            ->setParameter('search', '%' . $q . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();

        $result = [];
        foreach ($productos as $producto) {
            // Obtener todas las tallas para este producto
            $tallas = $em->getRepository('AppBundle:ProductoTalla')
                ->findBy(['producto' => $producto]);
            
            // Obtener todos los colores para este producto
            $colores = $em->getRepository('AppBundle:ProductoColor')
                ->findBy(['producto' => $producto]);

            foreach ($tallas as $talla) {
                foreach ($colores as $color) {
                    $name = $producto->getReferencia() . ' - ' . $producto->getNombre() . ' Talla: ' . $talla->getNombre() . ' Color: ' . $color->getNombre();
                    
                    $result[] = [
                        'id' => $producto->getId() . '_' . $talla->getId() . '_' . $color->getId(),
                        'text' => $name,
                        'referencia' => $producto->getReferencia(),
                        'nombre' => $producto->getNombre(),
                        'talla_id' => $talla->getId(),
                        'talla_nombre' => $talla->getNombre(),
                        'color_id' => $color->getId(),
                        'color_nombre' => $color->getNombre(),
                        'producto_id' => $producto->getId()
                    ];
                }
            }
        }

        return new JsonResponse($result);
    }

    /**
     * Creates a form to delete a supplierInvoice entity.
     */
    private function createDeleteForm(SupplierInvoice $supplierInvoice)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supplierinvoice_delete', array('id' => $supplierInvoice->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Updates product inventory for a specific invoice item
     * Este método ahora sigue la lógica del DespachoOrdenController para consistencia
     */
    private function updateProductInventoryForItem(SupplierInvoiceItem $item, $user, $fecha, SupplierInvoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        
        // Obtener la talla y el color del producto
        $productoTalla = $em->getRepository('AppBundle:ProductoTalla')->find($item->getProductoTallaId());
        $productoColor = $em->getRepository('AppBundle:ProductoColor')->find($item->getProductoColorId());
        
        if (!$productoTalla || !$productoColor) {
            throw new \Exception('Producto, talla o color no encontrado para el item de la factura');
        }

        $quantity = floatval($item->getQuantity());
        
        // Buscar si ya existe inventario para este producto/talla/color
        $existencia = $em->getRepository('AppBundle:ProductoInventario')
            ->findOneBy(['producto' => $productoTalla, 'color' => $productoColor]);

        if ($existencia == null) {
            // Crear nueva entrada de inventario
            $productoInventario = new ProductoInventario();
            $productoInventario->setProducto($productoTalla);
            $productoInventario->setColor($productoColor);
            $productoInventario->setIngresoDetal($quantity);
            $productoInventario->setEgresoDetal(0);
            $productoInventario->setQtyActualDetal($quantity);
            $productoInventario->setIngresoMayorista($quantity);
            $productoInventario->setEgresoMayorista(0);
            $productoInventario->setQtyActualMayorista($quantity);
            $productoInventario->setUltimoIngresoM($fecha);
            $em->persist($productoInventario);
        } else {
            // Actualizar inventario existente
            $nuevoStockMayorista = $existencia->getQtyActualMayorista() + $quantity;
            $existencia->setIngresoMayorista($existencia->getIngresoMayorista() + $quantity);
            $existencia->setQtyActualMayorista($nuevoStockMayorista);
            $existencia->setQtyActualDetal($nuevoStockMayorista);
            $existencia->setUltimoIngresoM($fecha);
            $em->persist($existencia);
        }

        // Registrar movimiento de inventario
        $inventarioMovimiento = new ProductoInventarioMovimiento();
        $inventarioMovimiento->setProducto($productoTalla->getProducto()->getNombre() . " Talla: " . $productoTalla->getNombre());
        $inventarioMovimiento->setColor($productoColor->getNombre());
        $inventarioMovimiento->setMovimiento("Ingreso");
        $inventarioMovimiento->setCantidad($quantity);
        $inventarioMovimiento->setBodega("mayorista");
        $inventarioMovimiento->setInformacion("Ingreso por factura proveedor: " . $invoice->getInvoiceNumber());
        $inventarioMovimiento->setUsuario($user->getName());
        $inventarioMovimiento->setFecha($fecha);
        $em->persist($inventarioMovimiento);

        $em->flush();
    }

    /**
     * Generates a unique file name
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

    /**
     * Finds and displays a supplierInvoice entity.
     */
    public function showAction(SupplierInvoice $supplierInvoice)
    {
        $em = $this->getDoctrine()->getManager();
        
        // Cargar productos con tallas y colores para mostrar los items
        $productos = $em->getRepository('AppBundle:Producto')
            ->createQueryBuilder('p')
            ->leftJoin('p.tallas', 't')
            ->leftJoin('p.colores', 'c')
            ->addSelect('t', 'c')
            ->getQuery()
            ->getResult();
        
        return $this->render('supplierinvoice/show.html.twig', array(
            'supplierInvoice' => $supplierInvoice,
            'productos' => $productos
        ));
    }

    /**
     * Creates a new supplierInvoice entity.
     */
    public function newAction(Request $request)
    {
        $supplierInvoice = new SupplierInvoice();
        $form = $this->createForm('AppBundle\Form\SupplierInvoiceType', $supplierInvoice);
        $form->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();
        
        $em = $this->getDoctrine()->getManager();
        
        // Cargar productos con tallas y colores para el formulario
        $productos = $em->getRepository('AppBundle:Producto')
            ->createQueryBuilder('p')
            ->leftJoin('p.tallas', 't')
            ->leftJoin('p.colores', 'c')
            ->addSelect('t', 'c')
            ->getQuery()
            ->getResult();

        if ($form->isSubmitted() && $form->isValid()) {
            // Validar número de factura único por proveedor
            $existingInvoice = $em->getRepository('AppBundle:SupplierInvoice')
                ->findOneBy([
                    'invoiceNumber' => $supplierInvoice->getInvoiceNumber(),
                    'proveedor' => $supplierInvoice->getProveedor()
                ]);

            if ($existingInvoice) {
                $this->addFlash('error', 'Ya existe una factura con este número para el proveedor seleccionado');
                return $this->render('supplierinvoice/new.html.twig', array(
                    'supplierInvoice' => $supplierInvoice,
                    'form' => $form->createView(),
                    'productos' => $productos
                ));
            }

            // Procesar archivo adjunto
            $file = $form['attachment']->getData();
            if ($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                $uploadsDir = $this->getParameter('kernel.root_dir') . '/../web/uploads/supplier_invoices';
                
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }
                
                $file->move($uploadsDir, $fileName);
                $supplierInvoice->setAttachmentPath('uploads/supplier_invoices/' . $fileName);
            }

            $supplierInvoice->setUsuarioCreacion($user);
            $supplierInvoice->setFechaCreacion($fecha);
            $supplierInvoice->setEstado(SupplierInvoice::STATUS_DRAFT);
            
            // Inicializar totales en 0
            $supplierInvoice->setSubtotal(0);
            $supplierInvoice->setTotalAmount(0);

            // IMPORTANTE: Primero persistir la factura para obtener su ID
            $em->persist($supplierInvoice);
            $em->flush();

            // Obtener datos de items del request
            $itemsData = $request->request->get('supplier_invoice')['items'] ?? [];

            // Procesar items manualmente
            $subtotal = 0;
            $itemsTaxTotal = 0;
            
            if (!empty($itemsData)) {
                foreach ($itemsData as $itemData) {
                    // Validar que tenga datos
                    if (empty($itemData['productoId']) || empty($itemData['quantity']) || empty($itemData['unitPrice'])) {
                        continue;
                    }

                    $item = new SupplierInvoiceItem();
                    $item->setSupplierInvoice($supplierInvoice);
                    $item->setProductoId($itemData['productoId']);
                    $item->setProductoTallaId($itemData['productoTallaId']);
                    $item->setProductoColorId($itemData['productoColorId']);
                    $item->setQuantity($itemData['quantity']);
                    $item->setUnitPrice($itemData['unitPrice']);
                    
                    // Establecer tasa de impuesto del item (0% o 19%)
                    $taxRate = isset($itemData['taxRate']) ? floatval($itemData['taxRate']) : 0;
                    $item->setTax($taxRate);
                    
                    // Calcular subtotal del item (sin impuesto)
                    $itemSubtotal = floatval($itemData['quantity']) * floatval($itemData['unitPrice']);
                    $subtotal += $itemSubtotal;
                    
                    // Calcular impuesto del item
                    $itemTax = $itemSubtotal * ($taxRate / 100);
                    $itemsTaxTotal += $itemTax;
                    
                    // El precio total del item incluye el impuesto
                    $item->setTotalPrice($itemSubtotal + $itemTax);
                    
                    if (isset($itemData['notes'])) {
                        $item->setNotes($itemData['notes']);
                    }

                    $em->persist($item);
                }
            }

            // Calcular y guardar totales
            $additionalTaxes = floatval($supplierInvoice->getTaxAmount() ?? 0);
            $logisticCosts = floatval($supplierInvoice->getLogisticCosts() ?? 0);
            $totalAmount = $subtotal + $itemsTaxTotal + $additionalTaxes + $logisticCosts;
            
            $supplierInvoice->setSubtotal($subtotal);
            $supplierInvoice->setTotalAmount($totalAmount);

            $em->flush();

            $this->addFlash('success', 'Factura creada correctamente');
            return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
        }

        return $this->render('supplierinvoice/new.html.twig', array(
            'supplierInvoice' => $supplierInvoice,
            'form' => $form->createView(),
            'productos' => $productos
        ));
    }

    /**
     * Displays a form to edit an existing supplierInvoice entity.
     */
    public function editAction(Request $request, SupplierInvoice $supplierInvoice)
    {
        if ($supplierInvoice->getEstado() == 0) {
            $this->addFlash('error', 'No se puede editar una factura cancelada');
            return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
        }

        if ($supplierInvoice->getEstado() != 1) {
            $this->addFlash('error', 'Solo se pueden editar facturas en estado borrador');
            return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
        }

        if ($supplierInvoice->getInventoryUpdated()) {
            $this->addFlash('error', 'No se puede editar una factura que ya ha actualizado el inventario');
            return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        $productos = $em->getRepository('AppBundle:Producto')
            ->createQueryBuilder('p')
            ->leftJoin('p.tallas', 't')
            ->leftJoin('p.colores', 'c')
            ->addSelect('t', 'c')
            ->getQuery()
            ->getResult();

        $originalItems = new ArrayCollection();
        foreach ($supplierInvoice->getItems() as $item) {
            $originalItems->add($item);
        }

        $editForm = $this->createForm('AppBundle\Form\SupplierInvoiceType', $supplierInvoice);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // Validar número de factura único por proveedor (excluyendo la actual)
            $existingInvoice = $em->getRepository('AppBundle:SupplierInvoice')
                ->createQueryBuilder('si')
                ->where('si.invoiceNumber = :invoiceNumber')
                ->andWhere('si.proveedor = :proveedor')
                ->andWhere('si.id != :currentId')
                ->setParameter('invoiceNumber', $supplierInvoice->getInvoiceNumber())
                ->setParameter('proveedor', $supplierInvoice->getProveedor())
                ->setParameter('currentId', $supplierInvoice->getId())
                ->getQuery()
                ->getOneOrNullResult();

            if ($existingInvoice) {
                $this->addFlash('error', 'Ya existe una factura con este número para el proveedor seleccionado');
                return $this->render('supplierinvoice/edit.html.twig', array(
                    'supplierInvoice' => $supplierInvoice,
                    'edit_form' => $editForm->createView(),
                    'productos' => $productos
                ));
            }

            // Procesar archivo adjunto
            $file = $editForm['attachment']->getData();
            if ($file) {
                // Eliminar archivo anterior si existe
                if ($supplierInvoice->getAttachmentPath()) {
                    $oldFile = $this->getParameter('kernel.root_dir') . '/../web/' . $supplierInvoice->getAttachmentPath();
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                $uploadsDir = $this->getParameter('kernel.root_dir') . '/../web/uploads/supplier_invoices';
                
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }
                
                $file->move($uploadsDir, $fileName);
                $supplierInvoice->setAttachmentPath('uploads/supplier_invoices/' . $fileName);
            }

            // Manejar items eliminados
            foreach ($originalItems as $item) {
                if (false === $supplierInvoice->getItems()->contains($item)) {
                    $supplierInvoice->removeItem($item);
                    $em->remove($item);
                }
            }

            // Obtener datos de items del request
            $itemsData = $request->request->get('supplier_invoice')['items'] ?? [];

            // Calcular totales
            $subtotal = 0;
            $itemsTaxTotal = 0;

            // Actualizar items existentes y crear nuevos
            if (!empty($itemsData)) {
                foreach ($itemsData as $index => $itemData) {
                    // Validar que tenga datos mínimos
                    if (empty($itemData['productoId']) || empty($itemData['quantity']) || empty($itemData['unitPrice'])) {
                        continue;
                    }

                    // Buscar si es un item existente o nuevo
                    $item = null;
                    foreach ($supplierInvoice->getItems() as $existingItem) {
                        if ($existingItem->getId() && isset($itemData['id']) && $existingItem->getId() == $itemData['id']) {
                            $item = $existingItem;
                            break;
                        }
                    }

                    // Si no existe, crear nuevo
                    if (!$item) {
                        $item = new SupplierInvoiceItem();
                        $item->setSupplierInvoice($supplierInvoice);
                        $supplierInvoice->addItem($item);
                    }

                    // Actualizar datos del item
                    $item->setProductoId($itemData['productoId']);
                    $item->setProductoTallaId($itemData['productoTallaId']);
                    $item->setProductoColorId($itemData['productoColorId']);
                    $item->setQuantity($itemData['quantity']);
                    $item->setUnitPrice($itemData['unitPrice']);
                    
                    // Establecer tasa de impuesto del item (0% o 19%)
                    $taxRate = isset($itemData['taxRate']) ? floatval($itemData['taxRate']) : 0;
                    $item->setTax($taxRate);
                    
                    // Calcular subtotal del item (sin impuesto)
                    $itemSubtotal = floatval($itemData['quantity']) * floatval($itemData['unitPrice']);
                    $subtotal += $itemSubtotal;
                    
                    // Calcular impuesto del item
                    $itemTax = $itemSubtotal * ($taxRate / 100);
                    $itemsTaxTotal += $itemTax;
                    
                    // El precio total del item incluye el impuesto
                    $item->setTotalPrice($itemSubtotal + $itemTax);
                    
                    if (isset($itemData['notes'])) {
                        $item->setNotes($itemData['notes']);
                    }

                    $em->persist($item);
                }
            }

            // Calcular y guardar totales de la factura
            $additionalTaxes = floatval($supplierInvoice->getTaxAmount() ?? 0);
            $logisticCosts = floatval($supplierInvoice->getLogisticCosts() ?? 0);
            $totalAmount = $subtotal + $itemsTaxTotal + $additionalTaxes + $logisticCosts;
            
            $supplierInvoice->setSubtotal($subtotal);
            $supplierInvoice->setTotalAmount($totalAmount);
            $supplierInvoice->setUsuarioActualizacion($user);
            $supplierInvoice->setFechaActualizacion($fecha);

            $em->flush();

            $this->addFlash('success', 'Factura editada correctamente');
            return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
        }

        return $this->render('supplierinvoice/edit.html.twig', array(
            'supplierInvoice' => $supplierInvoice,
            'edit_form' => $editForm->createView(),
            'productos' => $productos
        ));
    }

    /**
     * Marks a supplier invoice as paid
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN_VENTAS')")
     */
    public function markAsPaidAction(SupplierInvoice $supplierInvoice)
    {
        if ($supplierInvoice->getEstado() != SupplierInvoice::STATUS_CONFIRMED) {
            $this->addFlash('error', 'Solo las facturas confirmadas pueden marcarse como pagadas');
            return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        $supplierInvoice->setEstado(SupplierInvoice::STATUS_PAID);
        $supplierInvoice->setUsuarioActualizacion($user);
        $supplierInvoice->setFechaActualizacion($fecha);

        $em->flush();

        $this->addFlash('success', 'Factura marcada como pagada correctamente');
        return $this->redirectToRoute('supplierinvoice_show', ['id' => $supplierInvoice->getId()]);
    }
}