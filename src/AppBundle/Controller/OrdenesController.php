<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\DisenoOrden;

class OrdenesController extends Controller
{
    /**
     * Página principal del dashboard
     */
    public function indexAction(Request $request)
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * Obtiene los datos del dashboard para el período seleccionado
     */
    public function getDashboardDataAction(Request $request)
    {
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        
        try {
            $mes = $request->request->get('mes');
            $anio = $request->request->get('anio');
            
            if (!$mes || !$anio) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Mes y año son requeridos'
                ], 400);
            }

            $em = $this->getDoctrine()->getManager();
            
            $estadosUSA = $this->getEstadosUSAMap();
            
            $startDate = new \DateTime("$anio-$mes-01 00:00:00");
            $endDate = clone $startDate;
            $endDate->modify('last day of this month');
            $endDate->setTime(23, 59, 59);

            $orders = $em->getRepository('AppBundle:DespachoOrden')
                ->createQueryBuilder('a')
                ->where('a.statusPago IN (:estados)')
                ->setParameter('estados', [2, 3])
                ->andWhere('a.fechaPago >= :start OR a.fechaAbono1 >= :start OR a.fechaAbono2 >= :start')
                ->setParameter('start', $startDate)
                ->andWhere('a.fechaPago <= :finish OR a.fechaAbono1 <= :finish OR a.fechaAbono2 <= :finish')
                ->setParameter('finish', $endDate)
                ->leftJoin('a.clienteId', 'c')
                ->andWhere('(c.asesor IS NULL OR LOWER(c.asesor) != :asesor)')
                ->setParameter('asesor', 'mateo castro')
                ->getQuery()
                ->getResult();

            $totalVentas = 0; 
            $totalOrdenes = 0;
            $totalProductos = 0;
            $totalDinero = 0;

            $ventasPorEstado = [];
            $ventasPorDepartamento = [];
            $productosCantidad = [];
            $categoriaVentas = [];
            $coordenadasVentas = [];

            $ordenesUnicas = [];
            $transacciones = [];

            foreach ($orders as $order) {
                if (!$order->getClienteId()) {
                    continue;
                }

                $cliente = $order->getClienteId();

                $estadoInicial = 'XX';
                $estadoNombre = 'Sin Estado';
                $estadoLat = null;
                $estadoLng = null;
                
                try {
                    if (method_exists($cliente, 'getEstado') && $cliente->getEstado()) {
                        $estadoInicial = strtoupper(trim($cliente->getEstado()));

                        if (isset($estadosUSA[$estadoInicial])) {
                            $estadoNombre = $estadosUSA[$estadoInicial]['nombre'];
                            $estadoLat = $estadosUSA[$estadoInicial]['lat'];
                            $estadoLng = $estadosUSA[$estadoInicial]['lng'];
                        } else {
                            $estadoNombre = $estadoInicial;
                        }
                    }
                } catch (\Exception $e) {
                }
                
                $departamento = $estadoNombre;

                $latitud = null;
                $longitud = null;
                
                if (method_exists($cliente, 'getLatitud')) {
                    $latitud = $cliente->getLatitud();
                }
                if (method_exists($cliente, 'getLongitud')) {
                    $longitud = $cliente->getLongitud();
                }

                $orderItems = $em->getRepository('AppBundle:DespachoOrdenItem')
                    ->createQueryBuilder('a')
                    ->where('a.ordenDespacho = :orden')
                    ->setParameter('orden', $order)
                    ->getQuery()
                    ->getResult();

                $tieneAbonos = ($order->getAbono1() > 0 || $order->getAbono2() > 0);

                if ($order->getStatusPago() == 2 && $order->getFechaPago() && 
                    $order->getFechaPago() >= $startDate && 
                    $order->getFechaPago() <= $endDate &&
                    !$tieneAbonos) {
                    
                    $montoTransaccion = $order->getTotal();
                    
                    $transacciones[] = [
                        'monto' => $montoTransaccion,
                        'orden_id' => $order->getId(),
                        'estado_nombre' => $estadoNombre,
                        'estado_inicial' => $estadoInicial,
                        'estado_lat' => $estadoLat,
                        'estado_lng' => $estadoLng,
                        'departamento' => $departamento,
                        'latitud' => $latitud,
                        'longitud' => $longitud,
                        'order_total' => $order->getTotal(),
                        'items' => $orderItems
                    ];
                }

                if ($order->getFechaAbono1() && 
                    $order->getFechaAbono1() >= $startDate && 
                    $order->getFechaAbono1() <= $endDate &&
                    $order->getAbono1() > 0) {
                    
                    $montoTransaccion = $order->getAbono1();
                    
                    $transacciones[] = [
                        'monto' => $montoTransaccion,
                        'orden_id' => $order->getId(),
                        'estado_nombre' => $estadoNombre,
                        'estado_inicial' => $estadoInicial,
                        'estado_lat' => $estadoLat,
                        'estado_lng' => $estadoLng,
                        'departamento' => $departamento,
                        'latitud' => $latitud,
                        'longitud' => $longitud,
                        'order_total' => $order->getTotal(),
                        'items' => $orderItems
                    ];
                }

                if ($order->getFechaAbono2() && 
                    $order->getFechaAbono2() >= $startDate && 
                    $order->getFechaAbono2() <= $endDate &&
                    $order->getAbono2() > 0) {
                    
                    $montoTransaccion = $order->getAbono2();
                    
                    $transacciones[] = [
                        'monto' => $montoTransaccion,
                        'orden_id' => $order->getId(),
                        'estado_nombre' => $estadoNombre,
                        'estado_inicial' => $estadoInicial,
                        'estado_lat' => $estadoLat,
                        'estado_lng' => $estadoLng,
                        'departamento' => $departamento,
                        'latitud' => $latitud,
                        'longitud' => $longitud,
                        'order_total' => $order->getTotal(),
                        'items' => $orderItems
                    ];
                }

                if ($order->getStatusPago() == 2 && $order->getFechaPago() && 
                    $order->getFechaPago() >= $startDate && 
                    $order->getFechaPago() <= $endDate &&
                    $tieneAbonos) {
                    
                    $montoRestante = $order->getTotal() - $order->getTotalAbonos();
                    
                    if ($montoRestante > 0) {
                        $transacciones[] = [
                            'monto' => $montoRestante,
                            'orden_id' => $order->getId(),
                            'estado_nombre' => $estadoNombre,
                            'estado_inicial' => $estadoInicial,
                            'estado_lat' => $estadoLat,
                            'estado_lng' => $estadoLng,
                            'departamento' => $departamento,
                            'latitud' => $latitud,
                            'longitud' => $longitud,
                            'order_total' => $order->getTotal(),
                            'items' => $orderItems
                        ];
                    }
                }
            }

            foreach ($transacciones as $transaccion) {
                $montoTransaccion = $transaccion['monto'];
                $ordenId = $transaccion['orden_id'];
                $estadoNombre = $transaccion['estado_nombre'];
                $estadoInicial = $transaccion['estado_inicial'];
                $estadoLat = $transaccion['estado_lat'];
                $estadoLng = $transaccion['estado_lng'];
                $departamento = $transaccion['departamento'];
                $latitud = $transaccion['latitud'];
                $longitud = $transaccion['longitud'];
                $orderTotal = $transaccion['order_total'];
                $orderItems = $transaccion['items'];

                $totalVentas++;

                if (!isset($ordenesUnicas[$ordenId])) {
                    $ordenesUnicas[$ordenId] = true;
                    $totalOrdenes++;
                }

                $totalDinero += $montoTransaccion;

                if (!isset($ventasPorEstado[$estadoNombre])) {
                    $ventasPorEstado[$estadoNombre] = [
                        'total' => 0,
                        'ordenes' => 0,
                        'lat' => $estadoLat,
                        'lng' => $estadoLng,
                        'inicial' => $estadoInicial
                    ];
                }
                $ventasPorEstado[$estadoNombre]['total'] += $montoTransaccion;
                $ventasPorEstado[$estadoNombre]['ordenes']++;

                if (!isset($ventasPorDepartamento[$departamento])) {
                    $ventasPorDepartamento[$departamento] = [
                        'total' => 0,
                        'ordenes' => 0
                    ];
                }
                $ventasPorDepartamento[$departamento]['total'] += $montoTransaccion;
                $ventasPorDepartamento[$departamento]['ordenes']++;

                if ($latitud && $longitud) {
                    $coordenadasVentas[] = [
                        'lat' => (float)$latitud,
                        'lng' => (float)$longitud,
                        'monto' => $montoTransaccion,
                        'estado' => $estadoNombre
                    ];
                }

                if ($orderTotal == 0) {
                    continue;
                }

                $factorProporcional = $montoTransaccion / $orderTotal;

                foreach ($orderItems as $item) {
                    $producto = $item->getProducto()->getProducto();
                    $referencia = $producto->getReferencia();

                    $categoriaNombre = 'Sin Categoría';
                    if (method_exists($producto, 'getCategoria') && $producto->getCategoria()) {
                        $categoria = $producto->getCategoria();
                        if (method_exists($categoria, 'getNOmbrees')) {
                            $categoriaNombre = $categoria->getNOmbrees();
                        } elseif (method_exists($categoria, 'getNombre')) {
                            $categoriaNombre = $categoria->getNombre();
                        }
                    }

                    $cantidadProporcional = $item->getCantidad() * $factorProporcional;
                    $totalProporcional = $item->getPrecio() * $item->getCantidad() * $factorProporcional;

                    $totalProductos += $cantidadProporcional;

                    if (!isset($productosCantidad[$referencia])) {
                        $productosCantidad[$referencia] = [
                            'nombre' => $producto->getNombre(),
                            'referencia' => $referencia,
                            'cantidad' => 0,
                            'total' => 0
                        ];
                    }
                    $productosCantidad[$referencia]['cantidad'] += $cantidadProporcional;
                    $productosCantidad[$referencia]['total'] += $totalProporcional;

                    if (!isset($categoriaVentas[$categoriaNombre])) {
                        $categoriaVentas[$categoriaNombre] = [
                            'nombre' => $categoriaNombre,
                            'total' => 0,
                            'cantidad' => 0
                        ];
                    }
                    $categoriaVentas[$categoriaNombre]['total'] += $totalProporcional;
                    $categoriaVentas[$categoriaNombre]['cantidad'] += $cantidadProporcional;
                }
            }

            uasort($productosCantidad, function($a, $b) {
                if ($b['total'] == $a['total']) {
                    return 0;
                }
                return ($b['total'] > $a['total']) ? 1 : -1;
            });
            $topProductos = array_slice($productosCantidad, 0, 5);

            uasort($categoriaVentas, function($a, $b) {
                if ($b['total'] == $a['total']) {
                    return 0;
                }
                return ($b['total'] > $a['total']) ? 1 : -1;
            });
            $topCategorias = array_slice($categoriaVentas, 0, 5);

            uasort($ventasPorDepartamento, function($a, $b) {
                if ($b['total'] == $a['total']) {
                    return 0;
                }
                return ($b['total'] > $a['total']) ? 1 : -1;
            });

            $response = [
                'success' => true,
                'metricas' => [
                    'total_ventas' => $totalVentas,
                    'total_ordenes' => $totalOrdenes, 
                    'total_productos' => round($totalProductos, 0),
                    'total_dinero' => round($totalDinero, 2)
                ],
                'top_productos' => array_values($topProductos),
                'top_categorias' => array_values($topCategorias),
                'ventas_departamento' => $ventasPorDepartamento,
                'ventas_estado' => $ventasPorEstado,
                'coordenadas_ventas' => $coordenadasVentas,
                'periodo' => [
                    'mes' => $mes,
                    'anio' => $anio,
                    'inicio' => $startDate->format('Y-m-d'),
                    'fin' => $endDate->format('Y-m-d')
                ]
            ];

            return new JsonResponse($response);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Mapa de estados de USA con coordenadas centrales
     */
    private function getEstadosUSAMap()
    {
        return [
            'AL' => ['nombre' => 'Alabama', 'lat' => 32.3182, 'lng' => -86.9023],
            'AK' => ['nombre' => 'Alaska', 'lat' => 64.2008, 'lng' => -149.4937],
            'AZ' => ['nombre' => 'Arizona', 'lat' => 34.0489, 'lng' => -111.0937],
            'AR' => ['nombre' => 'Arkansas', 'lat' => 35.2010, 'lng' => -91.8318],
            'CA' => ['nombre' => 'California', 'lat' => 36.7783, 'lng' => -119.4179],
            'CO' => ['nombre' => 'Colorado', 'lat' => 39.5501, 'lng' => -105.7821],
            'CT' => ['nombre' => 'Connecticut', 'lat' => 41.6032, 'lng' => -73.0877],
            'DE' => ['nombre' => 'Delaware', 'lat' => 38.9108, 'lng' => -75.5277],
            'FL' => ['nombre' => 'Florida', 'lat' => 27.6648, 'lng' => -81.5158],
            'GA' => ['nombre' => 'Georgia', 'lat' => 32.1656, 'lng' => -82.9001],
            'HI' => ['nombre' => 'Hawaii', 'lat' => 19.8968, 'lng' => -155.5828],
            'ID' => ['nombre' => 'Idaho', 'lat' => 44.0682, 'lng' => -114.7420],
            'IL' => ['nombre' => 'Illinois', 'lat' => 40.6331, 'lng' => -89.3985],
            'IN' => ['nombre' => 'Indiana', 'lat' => 40.2672, 'lng' => -86.1349],
            'IA' => ['nombre' => 'Iowa', 'lat' => 41.8780, 'lng' => -93.0977],
            'KS' => ['nombre' => 'Kansas', 'lat' => 39.0119, 'lng' => -98.4842],
            'KY' => ['nombre' => 'Kentucky', 'lat' => 37.8393, 'lng' => -84.2700],
            'LA' => ['nombre' => 'Louisiana', 'lat' => 30.9843, 'lng' => -91.9623],
            'ME' => ['nombre' => 'Maine', 'lat' => 45.2538, 'lng' => -69.4455],
            'MD' => ['nombre' => 'Maryland', 'lat' => 39.0458, 'lng' => -76.6413],
            'MA' => ['nombre' => 'Massachusetts', 'lat' => 42.4072, 'lng' => -71.3824],
            'MI' => ['nombre' => 'Michigan', 'lat' => 44.3148, 'lng' => -85.6024],
            'MN' => ['nombre' => 'Minnesota', 'lat' => 46.7296, 'lng' => -94.6859],
            'MS' => ['nombre' => 'Mississippi', 'lat' => 32.3547, 'lng' => -89.3985],
            'MO' => ['nombre' => 'Missouri', 'lat' => 37.9643, 'lng' => -91.8318],
            'MT' => ['nombre' => 'Montana', 'lat' => 46.8797, 'lng' => -110.3626],
            'NE' => ['nombre' => 'Nebraska', 'lat' => 41.4925, 'lng' => -99.9018],
            'NV' => ['nombre' => 'Nevada', 'lat' => 38.8026, 'lng' => -116.4194],
            'NH' => ['nombre' => 'New Hampshire', 'lat' => 43.1939, 'lng' => -71.5724],
            'NJ' => ['nombre' => 'New Jersey', 'lat' => 40.0583, 'lng' => -74.4057],
            'NM' => ['nombre' => 'New Mexico', 'lat' => 34.5199, 'lng' => -105.8701],
            'NY' => ['nombre' => 'New York', 'lat' => 43.2994, 'lng' => -74.2179],
            'NC' => ['nombre' => 'North Carolina', 'lat' => 35.7596, 'lng' => -79.0193],
            'ND' => ['nombre' => 'North Dakota', 'lat' => 47.5515, 'lng' => -101.0020],
            'OH' => ['nombre' => 'Ohio', 'lat' => 40.4173, 'lng' => -82.9071],
            'OK' => ['nombre' => 'Oklahoma', 'lat' => 35.4676, 'lng' => -97.5164],
            'OR' => ['nombre' => 'Oregon', 'lat' => 43.8041, 'lng' => -120.5542],
            'PA' => ['nombre' => 'Pennsylvania', 'lat' => 41.2033, 'lng' => -77.1945],
            'RI' => ['nombre' => 'Rhode Island', 'lat' => 41.5801, 'lng' => -71.4774],
            'SC' => ['nombre' => 'South Carolina', 'lat' => 33.8361, 'lng' => -81.1637],
            'SD' => ['nombre' => 'South Dakota', 'lat' => 43.9695, 'lng' => -99.9018],
            'TN' => ['nombre' => 'Tennessee', 'lat' => 35.5175, 'lng' => -86.5804],
            'TX' => ['nombre' => 'Texas', 'lat' => 31.9686, 'lng' => -99.9018],
            'UT' => ['nombre' => 'Utah', 'lat' => 39.3210, 'lng' => -111.0937],
            'VT' => ['nombre' => 'Vermont', 'lat' => 44.5588, 'lng' => -72.5778],
            'VA' => ['nombre' => 'Virginia', 'lat' => 37.4316, 'lng' => -78.6569],
            'WA' => ['nombre' => 'Washington', 'lat' => 47.7511, 'lng' => -120.7401],
            'WV' => ['nombre' => 'West Virginia', 'lat' => 38.5976, 'lng' => -80.4549],
            'WI' => ['nombre' => 'Wisconsin', 'lat' => 43.7844, 'lng' => -88.7879],
            'WY' => ['nombre' => 'Wyoming', 'lat' => 43.0760, 'lng' => -107.2903],
            'DC' => ['nombre' => 'District of Columbia', 'lat' => 38.9072, 'lng' => -77.0369],
        ];
    }
}