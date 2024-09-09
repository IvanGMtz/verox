<?php
namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;

use AppBundle\Entity\AlmacenZonaInventario;
use AppBundle\Entity\FosUser;

use AppBundle\Entity\OrdenCompraItem;
use AppBundle\Entity\InventarioMovimiento;
use AppBundle\Entity\OrdenCompraNovedad;
use AppBundle\Entity\Inventario;
use AppBundle\Entity\InventarioCosto;
use AppBundle\Entity\ProduccionOrden;

use AppBundle\Entity\InventarioOrdenItem;
use AppBundle\Entity\InventarioOrdenNovedad;
use AppBundle\Entity\ProduccionCostoMaterial;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class InventarioService {
  private $em;
  public function __construct(EntityManager $entityManager) {
      $this->em = $entityManager;
  }
  
  public function agregarInventario(OrdenCompraItem $ordenCompraItem, AlmacenZonaInventario $almacenZonaInventario, FosUser $user, $con_novedad = true){
    $em = $this->em;
    $fecha = new \DateTime();
    $checkAlmacenZonaInventario = $em->getRepository('AppBundle:AlmacenZonaInventario')
            ->createQueryBuilder('a')
            ->where('a.material = :material')
            ->andWhere('a.almacenZona = :almacenZona')
            ->setParameter('material', $ordenCompraItem->getMaterial())
            ->setParameter('almacenZona', $almacenZonaInventario->getAlmacenZona())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    if(is_null($checkAlmacenZonaInventario)){
      $checkAlmacenZonaInventario = new AlmacenZonaInventario();
      $checkAlmacenZonaInventario->setAlmacen($almacenZonaInventario->getAlmacenZona()->getAlmacen());
      $checkAlmacenZonaInventario->setAlmacenZona($almacenZonaInventario->getAlmacenZona());
      $checkAlmacenZonaInventario->setMaterial($ordenCompraItem->getMaterial());
      $checkAlmacenZonaInventario->setCantidadActual(0);
      $checkAlmacenZonaInventario->setEgresoTotal(0);
      $checkAlmacenZonaInventario->setIngresoTotal(0);
    }

    $checkAlmacenZonaInventario->setCantidadActual($checkAlmacenZonaInventario->getCantidadActual() + $almacenZonaInventario->getCantidadActual());
    $checkAlmacenZonaInventario->setIngresoTotal($checkAlmacenZonaInventario->getIngresoTotal() + $almacenZonaInventario->getCantidadActual());
    $checkAlmacenZonaInventario->setFechaUltimoIngreso($fecha);
    $em->persist($checkAlmacenZonaInventario);

    $inventarioMovimiento = new InventarioMovimiento();
    $inventarioMovimiento->setAlmacen($checkAlmacenZonaInventario->getAlmacen());
    $inventarioMovimiento->setAlmacenZona($checkAlmacenZonaInventario->getAlmacenZona());
    $inventarioMovimiento->setAlmacenZonaInventario($checkAlmacenZonaInventario);
    $inventarioMovimiento->setCantidad($almacenZonaInventario->getCantidadActual());
    $inventarioMovimiento->setEstado(1);
    $inventarioMovimiento->setFechaCreacion($fecha);
    $inventarioMovimiento->setUsuarioCreacion($user);
    $inventarioMovimiento->setTipo('INGRESO');
    $inventarioMovimiento->setDescripcion('Ingreso de inventario desde orden de compra #'.$ordenCompraItem->getOrdenCompra()->getId().', item#'.$ordenCompraItem->getId());
    $inventarioMovimiento->setRef1($ordenCompraItem->getOrdenCompra()->getId());
    $inventarioMovimiento->setRef2($ordenCompraItem->getId());
    $inventarioMovimiento->setMaterial($ordenCompraItem->getMaterial());
    $inventarioMovimiento->setValorUnitario($ordenCompraItem->getValorUnidad());
    $inventarioMovimiento->setValorTotal($ordenCompraItem->getValorTotal());
    $em->persist($inventarioMovimiento);

    $checkInventarioCosto = $em->getRepository('AppBundle:InventarioCosto')
    ->createQueryBuilder('a')
    ->where('a.material = :material')
    ->setParameter('material', $ordenCompraItem->getMaterial())
    ->andwhere('a.valorSinIva = :precio')
    ->setParameter('precio', $ordenCompraItem->getValorUnidad())
    ->andwhere('a.proveedor = :proveedor')
    ->setParameter('proveedor', $ordenCompraItem->getOrdenCompra()->getProveedor())
    ->setMaxResults(1)
    ->getQuery()
    ->getOneOrNullResult()
    ;
    if(is_null($checkInventarioCosto)){
      $inventarioCosto = new InventarioCosto();
      $inventarioCosto->setMaterial($ordenCompraItem->getMaterial());
      $inventarioCosto->setProveedor($ordenCompraItem->getOrdenCompra()->getProveedor());
      $inventarioCosto->setAlmacen($almacenZonaInventario->getAlmacen());
      $inventarioCosto->setZona($almacenZonaInventario->getAlmacenZona());
      $inventarioCosto->setIngreso($almacenZonaInventario->getCantidadActual());
      $inventarioCosto->setEgreso(0);
      $inventarioCosto->setCantidadActual($almacenZonaInventario->getCantidadActual());
      $inventarioCosto->setValorSinIva($ordenCompraItem->getValorUnidad());
      $inventarioCosto->setValorConIva($ordenCompraItem->getValorUnidad() * (1+ ($ordenCompraItem->getOrdenCompra()->getImpuesto()/100)));
      $inventarioCosto->setOrdenCompra($ordenCompraItem->getOrdenCompra());
      $inventarioCosto->setFechaUltimoIngreso($fecha);
      $em->persist($inventarioCosto);
      $em->flush();
    }
    else{
      $checkInventarioCosto->setIngreso($checkInventarioCosto->getIngreso() + $almacenZonaInventario->getCantidadActual());
      $checkInventarioCosto->setCantidadActual($checkInventarioCosto->getCantidadActual() + $almacenZonaInventario->getCantidadActual());
      $checkInventarioCosto->setFechaUltimoIngreso($fecha);
    }
    $checkInventario = $em->getRepository('AppBundle:Inventario')
            ->createQueryBuilder('a')
            ->where('a.material = :material')
            ->setParameter('material', $ordenCompraItem->getMaterial())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    if(is_null($checkInventario)){
      $checkInventario = new Inventario();
      $checkInventario->setMaterial($ordenCompraItem->getMaterial());
      $checkInventario->setCantidadActual(0);
      $checkInventario->setEgresoTotal(0);
      $checkInventario->setIngresoTotal(0);
      $checkInventario->setReserva(0);
    }

    $checkInventario->setCantidadActual($checkInventario->getCantidadActual() + $almacenZonaInventario->getCantidadActual());
    $checkInventario->setIngresoTotal($checkInventario->getIngresoTotal() + $almacenZonaInventario->getCantidadActual());
    $checkInventario->setFechaUltimoIngreso($fecha);
    $em->persist($checkInventario);
    $em->flush();

    $ordenCompraItem->setEnInventario($ordenCompraItem->getEnInventario() + $almacenZonaInventario->getCantidadActual());
    $ordenCompraItem->setFechaIngresoInventario($fecha);
    if($almacenZonaInventario->getCantidadActual() < $ordenCompraItem->getCantidad()) {
      if($ordenCompraItem->getEnInventario() < $ordenCompraItem->getCantidad()) {
        $ordenCompraItem->setEstado(3); // Parcial
        $em->persist($ordenCompraItem);
        $ordenCompra = $ordenCompraItem->getOrdenCompra();
        $ordenCompra->setTienePendientes(true);
        $em->persist($ordenCompra);
        $em->flush();
      }
      else {
        $ordenCompraItem->setEstado(2);
        $em->persist($ordenCompraItem);
        $ordenCompra = $ordenCompraItem->getOrdenCompra();
        $ordenCompra->setTienePendientes(false);
        $em->persist($ordenCompra);
        $em->flush();
      }
    }
    else{
      $ordenCompraItem->setEstado(2);
      $em->persist($ordenCompraItem);
    }
    if($con_novedad){
      $novedad = new OrdenCompraNovedad();
      $novedad->setFechaCreacion($fecha);
      $novedad->setTipo('ITEM INGRESO INVENTARIO');
      $novedad->setUsuarioCreacion($user);
      $novedad->setOrdenCompra($ordenCompraItem->getOrdenCompra());
      $novedad->setDescripcion('Item #'.$ordenCompraItem->getId().' -> '.$ordenCompraItem->getMaterial()->getNombre().' ('.$almacenZonaInventario->getCantidadActual().') ingresado a inventario, '.$almacenZonaInventario->getAlmacenZona()->getAlmacen()->getNombre().' ('.$almacenZonaInventario->getAlmacenZona()->__toString().')');
      $em->persist($novedad);
    }

    $em->flush();

    $ordenCompra = $ordenCompraItem->getOrdenCompra();
    $checkItemsAbiertos = $em->getRepository('AppBundle:OrdenCompraItem')
            ->createQueryBuilder('a')
            ->where('a.ordenCompra = :ordenCompra')
            ->andWhere('(a.estado = 1 OR a.estado = 3)')
            ->setParameter('ordenCompra', $ordenCompra)
            ->getQuery()
            ->getResult()
            ;
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

      $em->flush();
    }
    
    return true;
  }
  
  public function descargarInventario(InventarioOrdenItem $inventarioOrdenItem, InventarioCosto $inventarioCosto, FosUser $user, $con_novedad, $cantidad){
    $em = $this->em;
    $fecha = new \DateTime();
    
    if($inventarioCosto->getCantidadActual() < $cantidad){return false;}
    $total_cantidad = 0;
    $checkCantidades = $em->getRepository('AppBundle:InventarioMovimiento')
            ->createQueryBuilder('a')
            ->where('a.ref1 = :inventarioOrden')
            ->setParameter('inventarioOrden', $inventarioOrdenItem->getInventarioOrden()->getId())
            ->andWhere('a.ref2 = :item')
            ->setParameter('item', $inventarioOrdenItem->getId())
            ->andWhere('a.tipo = :type')
            ->setParameter('type', "EGRESO")
            ->getQuery()
            ->getResult()
            ;
    foreach ($checkCantidades as $item) {
      $total_cantidad += $item->getCantidad();
    }
    if($total_cantidad + $cantidad > $inventarioOrdenItem->getCantidad()){ return false;}

    $inventarioCosto->setCantidadActual($inventarioCosto->getCantidadActual() - $cantidad);
    $inventarioCosto->setEgreso($inventarioCosto->getEgreso() + $cantidad);
    $em->persist($inventarioCosto);

    $almacenZonaInventario = $em->getRepository('AppBundle:AlmacenZonaInventario')
      ->createQueryBuilder('a')
      ->where('a.almacenZona = :zona')
      ->setParameter('zona', $inventarioCosto->getZona())
      ->andWhere('a.material = :material')
      ->setParameter('material', $inventarioCosto->getMaterial())
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult()
      ;
    $almacenZonaInventario->setCantidadActual($almacenZonaInventario->getCantidadActual() - $cantidad);
    $almacenZonaInventario->setEgresoTotal($almacenZonaInventario->getEgresoTotal() + $cantidad);
    $almacenZonaInventario->setFechaUltimoEgreso($fecha);
    $em->persist($almacenZonaInventario);


    $inventarioMovimiento = new InventarioMovimiento();
    $inventarioMovimiento->setAlmacen($inventarioCosto->getAlmacen());
    $inventarioMovimiento->setAlmacenZona($inventarioCosto->getZona());
    $inventarioMovimiento->setAlmacenZonaInventario($almacenZonaInventario);
    $inventarioMovimiento->setCantidad($cantidad);
    $inventarioMovimiento->setEstado(1);
    $inventarioMovimiento->setFechaCreacion($fecha);
    $inventarioMovimiento->setUsuarioCreacion($user);
    $inventarioMovimiento->setTipo('EGRESO');
    $inventarioMovimiento->setDescripcion('Egreso de inventario desde orden de inventario #'.$inventarioOrdenItem->getInventarioOrden()->getId().', item#'.$inventarioOrdenItem->getId());
    $inventarioMovimiento->setRef1($inventarioOrdenItem->getInventarioOrden()->getId());
    $inventarioMovimiento->setRef2($inventarioOrdenItem->getId());
    $inventarioMovimiento->setMaterial($inventarioOrdenItem->getMaterial());
    $inventarioMovimiento->setValorUnitario($inventarioOrdenItem->getMaterial()->getCostoActual()?$inventarioOrdenItem->getMaterial()->getCostoActual():0);
    $inventarioMovimiento->setValorTotal($cantidad * $inventarioMovimiento->getValorUnitario());
    $em->persist($inventarioMovimiento);

    $checkInventario = $em->getRepository('AppBundle:Inventario')
            ->createQueryBuilder('a')
            ->where('a.material = :material')
            ->setParameter('material', $inventarioOrdenItem->getMaterial())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;

    $checkInventario->setCantidadActual($checkInventario->getCantidadActual() - $cantidad);
    $checkInventario->setEgresoTotal($checkInventario->getEgresoTotal() + $cantidad);
    $checkInventario->setFechaUltimoEgreso($fecha);
    $em->persist($checkInventario);

    if($total_cantidad + $cantidad == $inventarioOrdenItem->getCantidad()){
      $inventarioOrdenItem->setEntregado(true);
      $inventarioOrdenItem->setFechaEntregado($fecha);
      $inventarioOrdenItem->setEstado(2);
      $em->persist($inventarioOrdenItem);
    }
    
    if($con_novedad){
      $novedad = new InventarioOrdenNovedad();
      $novedad->setFechaCreacion($fecha);
      $novedad->setTipo('ITEM EGRESO INVENTARIO');
      $novedad->setUsuarioCreacion($user);
      $novedad->setInventarioOrden($inventarioOrdenItem->getInventarioOrden());
      $novedad->setDescripcion('Item #'.$inventarioOrdenItem->getId().' -> '.$inventarioOrdenItem->getMaterial()->getNombre().' ('.$cantidad.') egreso de inventario, '.$almacenZonaInventario->getAlmacenZona()->getAlmacen()->getNombre().' ('.$almacenZonaInventario->getAlmacenZona()->__toString().')');
      $em->persist($novedad);
    }

    $em->flush();

    $inventarioOrden = $inventarioOrdenItem->getInventarioOrden();

    if($inventarioOrden->getRef3() != null){
      $ordenProduccion = $em->getRepository(ProduccionOrden::class)->find($inventarioOrden->getRef3());
      $checkCostoMaterial = $em->getRepository('AppBundle:ProduccionCostoMaterial')
      ->createQueryBuilder('a')
      ->where('a.material = :material')
      ->setParameter('material', $inventarioCosto)
      ->andwhere('a.ordenProduccion = :orden')
      ->setParameter('orden', $ordenProduccion)
      ->andwhere('a.diseno = :diseno')
      ->setParameter('diseno', $inventarioOrden->getRef1())
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      if($checkCostoMaterial == null){
        $costoMaterial = new ProduccionCostoMaterial();
        $costoMaterial->setMaterial($inventarioCosto);
        $costoMaterial->setCantidad($cantidad);
        $costoMaterial->setOrdenProduccion($ordenProduccion);
        $costoMaterial->setDiseno($inventarioOrden->getRef1());
        $em->persist($costoMaterial);
      }
      else{
        $checkCostoMaterial->setCantidad($checkCostoMaterial->getCantidad() + $cantidad);
        $em->persist($checkCostoMaterial);
      }
      $em->flush();
    }

    $checkItemsAbiertos = $em->getRepository('AppBundle:InventarioOrdenItem')
            ->createQueryBuilder('a')
            ->where('a.inventarioOrden = :inventarioOrden')
            ->andWhere('a.estado = 1')
            ->setParameter('inventarioOrden', $inventarioOrden)
            ->getQuery()
            ->getResult()
            ;
    if(count($checkItemsAbiertos) == 0){
      $inventarioOrden->setEstado(3);
      $inventarioOrden->setFechaRecibe($fecha);
      $inventarioOrden->setUsuarioRecibe($user);
      $em->persist($inventarioOrden);

      $novedadCierre = new InventarioOrdenNovedad();
      $novedadCierre->setFechaCreacion($fecha);
      $novedadCierre->setTipo('CERRADA');
      $novedadCierre->setUsuarioCreacion($user);
      $novedadCierre->setInventarioOrden($inventarioOrden);
      $novedadCierre->setDescripcion('Orden de inventario cerrada');
      $em->persist($novedadCierre);

      $em->flush();
    }
    
    return true;
  }
}
