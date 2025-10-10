<?php
namespace AppBundle\EventListener;

use AppBundle\Model\MenuItemModel;
use Avanzu\AdminThemeBundle\Event\SidebarMenuEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\EntityManagerInterface;

class MyMenuItemListListener {
    private $user;
    private $em;

    public function __construct(TokenStorage $tokenStorage, EntityManagerInterface $em){
      $this->em = $em;
      if($tokenStorage->getToken()){
        $this->user = $tokenStorage->getToken()->getUser();
      }else{
        $this->user = null;
      }
    }

    public function onSetupMenu(SidebarMenuEvent $event) {
        $request = $event->getRequest();
        foreach ($this->getMenu($request) as $item) {
            $event->addItem($item);
        }
    }

    protected function getMenu(Request $request) {
        $em = $this->em;
        $menuItems = array();
        
        // HOME - Para roles de producción
        if($this->user && !is_string($this->user) &&
          ($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE')
          || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA') || $this->user->hasRole('ROLE_INVENTARIO')
          || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO'))){
          array_push($menuItems, new MenuItemModel('diseno_home', 'Dashboard', 'ordenes_index', array(), 'fas fa-home'));
        }

        // MÓDULO DE TIENDA ONLINE
        if($this->user && !is_string($this->user) && ($this->user->hasRole('ROLE_ADMIN_VENTAS') || $this->user->hasRole('ROLE_SUPER_ADMIN'))){
          $tienda = new MenuItemModel('tienda_menu', 'Tienda Online', '', array(), 'fas fa-store-alt');
          $tienda->addChild(new MenuItemModel('storeinicio_index', 'Página Inicio', 'storeinicio_index', array(), 'fas fa-home'));
          $tienda->addChild(new MenuItemModel('storetienda_index', 'Página Tienda', 'storetienda_index', array(), 'fas fa-shopping-cart'));
          $tienda->addChild(new MenuItemModel('storeseo_edit', 'SEO', 'storeseo_edit', array("id"=>1), 'fas fa-search'));
          $tienda->addChild(new MenuItemModel('storebonos_index', 'Bonos de Compra', 'storebonos_index', array(), 'fas fa-ticket-alt'));
          $tienda->addChild(new MenuItemModel('storeusuarios_index', 'Clientes', 'storeusuarios_index', array(), 'fas fa-users'));
          array_push($menuItems, $tienda);
        }

        // MÓDULO DE PRODUCTOS
        if($this->user && !is_string($this->user) && 
           ($this->user->hasRole('ROLE_ADMIN_VENTAS') || $this->user->hasRole('ROLE_SUPER_ADMIN') || 
            $this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') ||
            $this->user->hasRole('ROLE_CORTE') || $this->user->hasRole('ROLE_INVENTARIO'))){
          $productos = new MenuItemModel('productos_menu', 'Productos', '', array(), 'fas fa-box-open');
          $productos->addChild(new MenuItemModel('producto_index', 'Lista de Productos', 'producto_index', array(), 'fas fa-list'));
          if($this->user->hasRole('ROLE_ADMIN_VENTAS') || $this->user->hasRole('ROLE_SUPER_ADMIN') || 
             $this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION')){
            $productos->addChild(new MenuItemModel('producto_new', 'Crear Producto', 'producto_new', array(), 'fas fa-plus-circle'));
          }
          if($this->user->hasRole('ROLE_ADMIN_VENTAS') || $this->user->hasRole('ROLE_SUPER_ADMIN')){
            $productos->addChild(new MenuItemModel('productocategoria_index', 'Categorías', 'productocategoria_index', array(), 'fas fa-tags'));
          }
          array_push($menuItems, $productos);
        }

        // MÓDULO DE INVENTARIO
        if($this->user && !is_string($this->user) && 
           ($this->user->hasRole('ROLE_ADMIN_VENTAS') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_INVENTARIO'))){
          $inventario = new MenuItemModel('inventario_menu', 'Inventario', '', array(), 'fas fa-warehouse');
          $inventario->addChild(new MenuItemModel('productoinventario_index', 'Stock Actual', 'productoinventario_index', array(), 'fas fa-boxes'));
          if($this->user->hasRole('ROLE_ADMIN_VENTAS') || $this->user->hasRole('ROLE_SUPER_ADMIN')){
            $inventario->addChild(new MenuItemModel('productoinventariomovimiento_index', 'Movimientos', 'productoinventariomovimiento_index', array(), 'fas fa-exchange-alt'));
          }
          array_push($menuItems, $inventario);
        }

        // MÓDULO DE DESPACHOS
        if($this->user && !is_string($this->user) && 
           ($this->user->hasRole('ROLE_ADMIN_VENTAS') || $this->user->hasRole('ROLE_SUPER_ADMIN') || 
            $this->user->hasRole('ROLE_VENDEDOR') || $this->user->hasRole('ROLE_DESPACHOS'))){
          $despachos = new MenuItemModel('despachos_menu', 'Despachos', '', array(), 'fas fa-shipping-fast');
          $despachos->addChild(new MenuItemModel('despachoorden_index', 'Órdenes de Despacho', 'despachoorden_index', array(), 'fas fa-clipboard-list'));
          $despachos->addChild(new MenuItemModel('despachoorden_new', 'Nueva Orden', 'despachoorden_new', array(), 'fas fa-plus-square'));
          array_push($menuItems, $despachos);
        }

        // MÓDULO DE DISEÑO Y PRODUCCIÓN
        if($this->user && !is_string($this->user) &&
           ($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION'))){
          $diseno = new MenuItemModel('diseno_menu', 'Diseño', '', array(), 'fas fa-palette');
          $diseno->addChild(new MenuItemModel('diseno_new', 'Crear Diseño', 'diseno_new', array(), 'fas fa-pencil-ruler'));
          $diseno->addChild(new MenuItemModel('produccion_index', 'Producción', 'produccion_index', array(), 'fas fa-industry'));
          array_push($menuItems, $diseno);
        }

        // MÓDULO DE ÓRDENES DE INVENTARIO (Producción)
        if($this->user && !is_string($this->user) &&
           ($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE') || $this->user->hasRole('ROLE_INVENTARIO')
           || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA')
           || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO'))){
          $ordInventario = new MenuItemModel('ord_inventario_menu', 'Órdenes Inventario', '', array(), 'fas fa-dolly');
          if($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE')
          || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA')
          || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO')){
            $ordInventario->addChild(new MenuItemModel('inventarioorden_new', 'Nueva Orden', 'inventarioorden_new', array(), 'fas fa-file-medical'));
          }
          $ordInventario->addChild(new MenuItemModel('inventarioorden_index', 'Todas las Órdenes', 'inventarioorden_index', array(), 'fas fa-list-ul'));
          if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION')){
            $ordInventario->addChild(new MenuItemModel('inventarioorden_pend_aceptar', 'Pendientes Aprobar', 'inventarioorden_pend_aceptar', array(), 'fas fa-hourglass-half'));
          }
          if($this->user->hasRole('ROLE_INVENTARIO') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_CORTE')){
            $ordInventario->addChild(new MenuItemModel('inventarioorden_pend_entrega', 'Pendientes Entrega', 'inventarioorden_pend_entrega', array(), 'fas fa-truck-loading'));
          }
          array_push($menuItems, $ordInventario);
        }

        // MÓDULO DE ÓRDENES DE COMPRA
        if($this->user && !is_string($this->user) &&
           ($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE') || $this->user->hasRole('ROLE_INVENTARIO')
           || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA')
           || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO'))){
          $ordCompra = new MenuItemModel('ord_compra_menu', 'Órdenes de Compra', '', array(), 'fas fa-shopping-basket');
          if($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE')
          || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA')
          || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO')){
            $ordCompra->addChild(new MenuItemModel('ordencompra_new', 'Nueva Orden', 'ordencompra_new', array(), 'fas fa-file-invoice'));
          }
          $ordCompra->addChild(new MenuItemModel('ordencompra_index', 'Todas las Órdenes', 'ordencompra_index', array(), 'fas fa-list-ul'));
          if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION')){
            $ordCompra->addChild(new MenuItemModel('ordencompra_pend_aceptar', 'Pendientes Aprobar', 'ordencompra_pend_aceptar', array(), 'fas fa-hourglass-half'));
          }
          if($this->user->hasRole('ROLE_INVENTARIO') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE')){
            $ordCompra->addChild(new MenuItemModel('ordencompra_pend_recibir', 'Pendientes Recibir', 'ordencompra_pend_recibir', array(), 'fas fa-box'));
          }
          array_push($menuItems, $ordCompra);
        }

        // MÓDULO DE PROVEEDORES
        if($this->user && !is_string($this->user) && 
           ($this->user->hasRole('ROLE_ADMIN_VENTAS') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_INVENTARIO'))){
          array_push($menuItems, new MenuItemModel('supplierinvoice_index', 'Facturas Proveedores', 'supplierinvoice_index', array(), 'fas fa-file-invoice-dollar'));
        }

        // MÓDULO DE REPORTES
        if($this->user && !is_string($this->user) && 
           ($this->user->hasRole('ROLE_ADMIN_VENTAS') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_DESPACHOS'))){
          array_push($menuItems, new MenuItemModel('store_reporte', 'Reportes', 'store_reporte', array(), 'fas fa-chart-line'));
        }

        // MÓDULO DE CONFIGURACIÓN
        if($this->user && !is_string($this->user)){
          $configuracion = new MenuItemModel('configuracion', 'Configuración', '', array(), 'fas fa-cog');
          
          if($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE') || $this->user->hasRole('ROLE_INVENTARIO')
          || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA')
          || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO')){
            $configuracion->addChild(new MenuItemModel('material_index', 'Materiales', 'material_index', array(), 'fas fa-box'));
            $configuracion->addChild(new MenuItemModel('inventariocosto_index2', 'Inventario', 'inventariocosto_index2', array(), 'fas fa-warehouse'));
            $configuracion->addChild(new MenuItemModel('inventariocosto_index', 'Costos de Inventario', 'inventariocosto_index', array(), 'fas fa-dollar-sign'));
            $configuracion->addChild(new MenuItemModel('almacen_index', 'Almacenes', 'almacen_index', array(), 'fas fa-building'));
            $configuracion->addChild(new MenuItemModel('proveedor_index', 'Proveedores', 'proveedor_index', array(), 'fas fa-truck'));
          }
          
          if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION')){
            $configuracion->addChild(new MenuItemModel('fosuser_index', 'Usuarios', 'fosuser_index', array(), 'fas fa-user-cog'));
            $configuracion->addChild(new MenuItemModel('equipotrabajo_index', 'Equipos de Trabajo', 'equipotrabajo_index', array(), 'fas fa-users-cog'));
          }
          
          $configuracion->addChild(new MenuItemModel('fosuser_edit', 'Mi Perfil', 'fosuser_edit', array('id' => $this->user->getId()), 'fas fa-user-circle'));
          
          array_push($menuItems, $configuracion);
        }

        return $this->activateByRoute($request->get('_route'), $menuItems);
    }

    protected function activateByRoute($route, $items) {
        foreach($items as $item) {
            if($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            }
            else {
                if($item->getRoute() == $route) {
                    $item->setIsActive(true);
                }
            }
        }
        return $items;
    }
}