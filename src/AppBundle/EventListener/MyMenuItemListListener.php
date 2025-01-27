<?php
namespace AppBundle\EventListener;

// ...

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
        
        if($this->user && !is_string($this->user) && ($this->user->hasRole('ROLE_ADMIN_VENTAS') || $this->user->hasRole('ROLE_SUPER_ADMIN'))){
          array_push($menuItems, $inventario = new MenuItemModel('productoinventario_index', 'INVENTARIO', 'productoinventario_index', array(), 'fas fa-cubes'));
          array_push($menuItems, $ventas_home = new MenuItemModel('productoinventariomovimiento_index', 'INVENTARIO MOVIMIENTO', 'productoinventariomovimiento_index', array(), 'fas fa-cubes'));
          array_push($menuItems, $productos = new MenuItemModel('producto_index', 'MIS PRODUCTOS', 'producto_index', array(), 'fas fa-shopping-bag'));
          array_push($menuItems, $producto_new = new MenuItemModel('producto_new', 'CREAR PRODUCTO', 'producto_new', array(), 'fas fa-female'));
          array_push($menuItems, $producto_categoria = new MenuItemModel('productocategoria_index', 'CATEGORIAS DE PRODUCTO', 'productocategoria_index', array(), 'fas fa-list-alt'));
          array_push($menuItems, $storeinicio_index = new MenuItemModel('storeinicio_index', 'PAGINA INICIO', 'storeinicio_index', array(), 'fas fa-store'));
          array_push($menuItems, $storetienda_index = new MenuItemModel('storetienda_index', 'PAGINA TIENDA', 'storetienda_index', array(), 'fas fa-store'));
          array_push($menuItems, $storebonos_index = new MenuItemModel('storebonos_index', 'BONOS DE COMPRA', 'storebonos_index', array(), 'fas fa-store'));
          array_push($menuItems, $storeusuarios_index = new MenuItemModel('storeusuarios_index', 'USUARIOS TIENDA', 'storeusuarios_index', array(), 'fas fa-store'));
          array_push($menuItems, $storeseo_edit = new MenuItemModel('storeseo_edit', 'TIENDA SEO', 'storeseo_edit', array("id"=>1), 'fas fa-store'));
          array_push($menuItems, $despacho_orden = new MenuItemModel('despachoorden_index', 'ORDENES DE DESPACHO', 'despachoorden_index', array(), 'fas fa-cubes'));
          array_push($menuItems, $despacho_orden_new = new MenuItemModel('despachoorden_new', 'NUEVA ORDEN DE DESPACHO', 'despachoorden_new', array(), 'fas fa-cubes'));
          array_push($menuItems, $reporte = new MenuItemModel('store_reporte', 'REPORTES', 'store_reporte', array(), 'fas fa-line-chart'));
        }
        if($this->user->hasRole('ROLE_INVENTARIO')){
          array_push($menuItems, $inventario = new MenuItemModel('productoinventario_index', 'INVENTARIO', 'productoinventario_index', array(), 'fas fa-cubes'));
        }
        if($this->user && !is_string($this->user) && ($this->user->hasRole('ROLE_VENDEDOR'))){
          // array_push($menuItems, $productos = new MenuItemModel('producto_index', 'MIS PRODUCTOS', 'producto_index', array(), 'fas fa-shopping-bag'));
          array_push($menuItems, $storebonos_index = new MenuItemModel('storebonos_index', 'BONOS DE COMPRA', 'storebonos_index', array(), 'fas fa-store'));
          array_push($menuItems, $storeusuarios_index = new MenuItemModel('storeusuarios_index', 'USUARIOS TIENDA', 'storeusuarios_index', array(), 'fas fa-store'));
          array_push($menuItems, $despacho_orden = new MenuItemModel('despachoorden_index', 'ORDENES DE DESPACHO', 'despachoorden_index', array(), 'fas fa-cubes'));
          // array_push($menuItems, $despacho_orden_new = new MenuItemModel('despachoorden_new', 'NUEVA ORDEN DE DESPACHO', 'despachoorden_new', array(), 'fas fa-cubes'));
        }
        if($this->user && !is_string($this->user) && ($this->user->hasRole('ROLE_DESPACHOS'))){
          // array_push($menuItems, $productos = new MenuItemModel('producto_index', 'MIS PRODUCTOS', 'producto_index', array(), 'fas fa-shopping-bag'));
          array_push($menuItems, $storebonos_index = new MenuItemModel('storebonos_index', 'BONOS DE COMPRA', 'storebonos_index', array(), 'fas fa-store'));
          array_push($menuItems, $storeusuarios_index = new MenuItemModel('storeusuarios_index', 'USUARIOS TIENDA', 'storeusuarios_index', array(), 'fas fa-store'));
          array_push($menuItems, $despacho_orden = new MenuItemModel('despachoorden_index', 'ORDENES DE DESPACHO', 'despachoorden_index', array(), 'fas fa-cubes'));
          array_push($menuItems, $despacho_orden_new = new MenuItemModel('despachoorden_new', 'NUEVA ORDEN DE DESPACHO', 'despachoorden_new', array(), 'fas fa-cubes'));
          array_push($menuItems, $reporte = new MenuItemModel('store_reporte', 'REPORTES', 'store_reporte', array(), 'fas fa-line-chart'));
        }
        if(
          $this->user && !is_string($this->user) &&
          ($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE')
          || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA') || $this->user->hasRole('ROLE_INVENTARIO')
          || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO'))
        ){
          array_push($menuItems, $diseno_home = new MenuItemModel('diseno_home', 'HOME', 'ordenes_index', array(), 'fas fa-home'));
        }
        if(
            $this->user && !is_string($this->user) &&
            ($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION'))
          ){
          array_push($menuItems, $diseno_new = new MenuItemModel('diseno_new', 'CREAR DISEÑO', 'diseno_new', array(), 'fas fa-female'));
          array_push($menuItems, $productos = new MenuItemModel('producto_index', 'MIS PRODUCTOS', 'producto_index', array(), 'fas fa-shopping-bag'));
          array_push($menuItems, $producto_new = new MenuItemModel('producto_new', 'CREAR PRODUCTO', 'producto_new', array(), 'fas fa-female'));
        }
        if(
            $this->user && !is_string($this->user) &&
            ($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION'))
          ){
          array_push($menuItems, $diseno_home = new MenuItemModel('produccion_index', 'PRODUCCIÓN', 'produccion_index', array(), 'fas fa-line-chart'));
        }
        if(
            $this->user && !is_string($this->user) &&
            ($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE') || $this->user->hasRole('ROLE_INVENTARIO')
            || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA')
            || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO'))
          ){
          if(($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE')
          || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA')
          || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO'))){
            array_push($menuItems, $inventarioorden_new = new MenuItemModel('inventarioorden_new', 'NUEVA ORDEN INVENTARIO', 'inventarioorden_new', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-cubes'));
          }
          array_push($menuItems, $inventarioorden_index = new MenuItemModel('inventarioorden_index', 'ÓRDENES INVENTARIO', 'inventarioorden_index', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-cubes'));
          if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION')){
            array_push($menuItems, $inventarioorden_pend_aceptar = new MenuItemModel('inventarioorden_pend_aceptar', 'PENDIENTES ACEPTAR', 'inventarioorden_pend_aceptar', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-exclamation'));
          }
          if($this->user->hasRole('ROLE_INVENTARIO')  || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_CORTE')){
            array_push($menuItems, $inventarioorden_pend_entrega = new MenuItemModel('inventarioorden_pend_entrega', 'PENDIENTES ENTREGA', 'inventarioorden_pend_entrega', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-exclamation'));
          }
          if(($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE')
          || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA')
          || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO'))){
            array_push($menuItems, $ordencompra_new = new MenuItemModel('ordencompra_new', 'NUEVA ORDEN COMPRA', 'ordencompra_new', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-shopping-bag'));
          }
          if(($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE') || $this->user->hasRole('ROLE_INVENTARIO')
          || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA')
          || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO'))){
            array_push($menuItems, $ordencompra_index = new MenuItemModel('ordencompra_index', 'ÓRDENES DE COMPRA', 'ordencompra_index', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-shopping-bag'));
          }
          if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION')){
            array_push($menuItems, $ordencompra_pend_aceptar = new MenuItemModel('ordencompra_pend_aceptar', 'PENDIENTES ACEPTAR', 'ordencompra_pend_aceptar', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-exclamation'));
          }
          if($this->user->hasRole('ROLE_INVENTARIO') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION')  || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE')){
            array_push($menuItems, $ordencompra_pend_recibir = new MenuItemModel('ordencompra_pend_recibir', 'PENDIENTES RECIBIR', 'ordencompra_pend_recibir', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-exclamation'));
          }
        }
        if(
            $this->user && !is_string($this->user)
          ){
          array_push($menuItems, $configuracion = new MenuItemModel('configuracion', 'CONFIGURACIÓN', '', array(), 'fas fa-cogs'));
          if(($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE') || $this->user->hasRole('ROLE_INVENTARIO')
          || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA')
          || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO'))){
            $configuracion->addChild(new MenuItemModel('material_index', 'MATERIALES', 'material_index', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-chart-line'));
            $configuracion->addChild(new MenuItemModel('inventariocosto_index2', 'INVENTARIO', 'inventariocosto_index2', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-chart-line'));
            $configuracion->addChild(new MenuItemModel('inventariocosto_index', 'COSTO DE INVENTARIO', 'inventariocosto_index', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-chart-line'));
          }
          if(($this->user->hasRole('ROLE_DESIGN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION') || $this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_CORTE')
          || $this->user->hasRole('ROLE_CONFECCION') || $this->user->hasRole('ROLE_BORDADO') || $this->user->hasRole('ROLE_LAVANDERIA') || $this->user->hasRole('ROLE_INVENTARIO')
          || $this->user->hasRole('ROLE_TERMINADOS') || $this->user->hasRole('ROLE_PRETERMINADOS') || $this->user->hasRole('ROLE_EMPAQUE') || $this->user->hasRole('ROLE_TRAZO'))){
            $configuracion->addChild(new MenuItemModel('almacen_index', 'ALMACENES', 'almacen_index', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-chart-line'));
            $configuracion->addChild(new MenuItemModel('proveedor_index', 'PROVEEDORES', 'proveedor_index', array(/**'rol'=>'punto_recaudo'**/), 'fas fa-chart-line'));
          }
          if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN_PRODUCCION')){
            $configuracion->addChild(new MenuItemModel('fosuser_index', 'USUARIOS', 'fosuser_index', array(/* options */), 'fa fa-users'));
            $configuracion->addChild(new MenuItemModel('equipotrabajo_index', 'EQUIPO DE TRABAJO', 'equipotrabajo_index', array(/* options */), 'fa fa-users'));
          }
          $configuracion->addChild(new MenuItemModel('fosuser_edit', 'EDITAR PERFIL', 'fosuser_edit', array('id' => $this->user->getId()), 'fa fa-edit'));
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
