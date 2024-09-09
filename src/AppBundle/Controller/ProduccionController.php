<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\ProduccionOrden;
use AppBundle\Entity\ProduccionCosto;
use AppBundle\Entity\ProduccionDiseno;
use AppBundle\Entity\DisenoOrden;
use AppBundle\Entity\ProcesoEncargado;
use AppBundle\Entity\Proceso;
use AppBundle\Entity\Diseno;


class ProduccionController extends Controller
{

    /**
     */
    public function indexAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $ordenesP = $em->getRepository('AppBundle:ProduccionOrden')
              ->createQueryBuilder('a')
              ->getQuery()
              ->getResult();
      $cantidad = [];
      $colecciones = [];
      $tiempos = [];
      $costos = [];
      foreach ($ordenesP as $orden) {
        if($orden->getEstado()==2){
          array_push($colecciones,$orden->getReferencia());
          array_push($tiempos,(($orden->getDuracion() / 60) / 24 > 1) ? floor((($orden->getDuracion() / 60) / 24)) : 0);
          $qty = 0;
          $items = $em->getRepository('AppBundle:ProduccionTalla')
          ->createQueryBuilder('a')
          ->where('a.ordenProduccion = :orden')
          ->setParameter('orden', $orden->getId())
          ->getQuery()
          ->getResult();
          foreach ($items as $item) {
            $qty = $qty + $item->getCantidadConfirmada();
          }
          array_push($cantidad,$qty);
        }
      }
      return $this->render('produccion/index.html.twig', array(
        'ordenes' => $ordenesP,
        'colecciones'=>json_encode($colecciones),
        'tiempos'=>json_encode($tiempos),
        'cantidades'=>json_encode($cantidad),
        'costos'=>json_encode($costos)
      ));
//        return $this->redirectToRoute('fos_user_security_login');
    }
    public function newOrdenDisenoAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();

      $registro1 = new DisenoOrden();
      $registro1->setReferencia($request->request->get('referencia'));
      $registro1->setCantidad($request->request->get('cantidad'));
      $registro1->setNotas($request->request->get('notas'));
      $registro1->setFechaCreacion($ahora);
      $registro1->setEstado(1);
      $registro1->setUsuarioCreacion($user);
      $em->persist($registro1);
      $em->flush();



      $this->addFlash(
        'success',
        'Orden Creada correctamente'
      );
      $ordenesP = $em->getRepository('AppBundle:ProduccionOrden')
              ->createQueryBuilder('a')
              ->getQuery()
              ->getResult();
      $cantidad = [];
      $colecciones = [];
      $tiempos = [];
      $costos = [];
      foreach ($ordenesP as $orden) {
        if($orden->getEstado()==2){
          array_push($colecciones,$orden->getReferencia());
          array_push($tiempos,(($orden->getDuracion() / 60) / 24 > 1) ? floor((($orden->getDuracion() / 60) / 24)) : 0);
          $qty = 0;
          $items = $em->getRepository('AppBundle:ProduccionTalla')
          ->createQueryBuilder('a')
          ->where('a.ordenProduccion = :orden')
          ->setParameter('orden', $orden->getId())
          ->getQuery()
          ->getResult();
          foreach ($items as $item) {
            $qty = $qty + $item->getCantidadConfirmada();
          }
          array_push($cantidad,$qty);
        }
      }
      return $this->render('produccion/index.html.twig', array(
        'ordenes' => $ordenesP,
        'colecciones'=>json_encode($colecciones),
        'tiempos'=>json_encode($tiempos),
        'cantidades'=>json_encode($cantidad),
        'costos'=>json_encode($costos)
      ));
    }

    public function preliquidarAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $proceso = $this->getDoctrine()->getRepository(Proceso::class)->find($request->request->get('proceso'));
      $tipoOrden = $request->request->get('tipo_orden');
      if ($tipoOrden == "PRODUCCION") {
        $orden = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
        $procesoNombre = $em->getRepository('AppBundle:ProcesoNombre')
                ->createQueryBuilder('a')
                ->where('a.nombre = :name')
                ->setParameter('name', $proceso->getProceso())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        $costo = $em->getRepository('AppBundle:ProduccionCosto')
                ->createQueryBuilder('a')
                ->where('a.ordenProduccion = :order')
                ->setParameter('order', $orden)
                ->andwhere('a.proceso = :process')
                ->setParameter('process', $procesoNombre->getId())
                ->getQuery()
                ->getResult();
      }
      else{
        $orden = $this->getDoctrine()->getRepository(DisenoOrden::class)->find($request->request->get('orden_diseno'));
        $procesoNombre = $em->getRepository('AppBundle:ProcesoNombre')
                ->createQueryBuilder('a')
                ->where('a.nombre = :name')
                ->setParameter('name', $proceso->getProceso())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        $diseno = $em->getRepository('AppBundle:Diseno')
                ->createQueryBuilder('a')
                ->where('a.orden = :order')
                ->setParameter('order', $orden)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        $costo = array('0'=>array('costo'=>$diseno->getCostoCorte(),'costo2'=>0,'costo3'=>0,'costo4'=>0));
      }
      $trabajadores = $em->getRepository('AppBundle:ProcesoEncargado')
              ->createQueryBuilder('a')
              ->join('a.proceso','p')
              ->where('p.proceso = :process')
              ->setParameter('process', $proceso->getProceso())
              ->andWhere('p.tipoOrden = :type')
              ->setParameter('type', $tipoOrden)
              ->andWhere('p.orden = :order')
              ->setParameter('order', $orden->getId())
              ->getQuery()
              ->getResult();
      #var_dump($costo);exit;
      return $this->render('produccion/preliquidar.html.twig', array(
        'trabajadores' => $trabajadores,
        'costo' => $costo,
        'proceso' => $proceso,
        'ordenProduccion'=>$orden,
        'tipoOrden'=>$tipoOrden
      ));
    }

}
