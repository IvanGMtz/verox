<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Diseno;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\DisenoMaterial;
use AppBundle\Entity\DisenoNovedad;
use AppBundle\Entity\Proceso;
use AppBundle\Entity\DisenoOrden;
use AppBundle\Entity\ProduccionOrden;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Diseno controller.
 *
 */
class DisenoController extends Controller
{
    /**
     * Lists all diseno entities.
     *
     */
    private function createDeleteForm(DisenoOrden $disenoOrden)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('disenoorden_delete', array('id' => $disenoOrden->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    public function indexAction(Request $request, DisenoOrden $orden ,PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($orden);
        $q = $request->request->get('q', $this->get('session')->get('q.Diseno'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        $disenosQ = $em->getRepository('AppBundle:Diseno')->createQueryBuilder('a')
        ->where('a.orden  = :order')->setParameter('order', $orden);

        if($q && $q !=''){
          $this->get('session')->set('q.Diseno', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $disenosQ->andwhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $disenosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }

        $query = $disenosQ->getQuery();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        $disenos = $pagination->getItems();
        $ordenes = $em->getRepository('AppBundle:DisenoOrden')
                ->createQueryBuilder('c')
                ->getQuery()
                ->getResult();
        $procesos_activos = $em->getRepository('AppBundle:Proceso')
                ->createQueryBuilder('a')
                ->where('a.status = 1')
                ->getQuery()
                ->getResult();
        $procesos_ended = $em->getRepository('AppBundle:Proceso')
                ->createQueryBuilder('a')
                ->where('a.status = 2')
                ->andWhere('a.tipoOrden = :order')
                ->setParameter('order', 'DISEÑO')
                ->andWhere('a.proceso = :process')
                ->setParameter('process', 'CORTE')
                ->andWhere('a.orden = :order2')
                ->setParameter('order2', $orden->getId())
                ->getQuery()
                ->getResult();
        return $this->render('diseno/index.html.twig', array(
            'disenos' => $disenos,
            'q' => $q,
            'pagination' => $pagination,
            'ordenes' => $ordenes,
            'procesos_activos' => $procesos_activos,
            'procesos_ended' => $procesos_ended,
            'orden' => $orden,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a new diseno entity.
     *
     */
    public function newAction(Request $request)
    {
        $diseno = new Diseno(); 
        $form = $this->createForm('AppBundle\Form\DisenoType', $diseno);
        $form->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $orden = $this->getDoctrine()->getRepository(DisenoOrden::class)->find($request->request->get('appbundle_diseno')['orden']);
            $disenos = $em->getRepository('AppBundle:Diseno')
                    ->createQueryBuilder('a')
                    ->where('a.orden = :order')
                    ->setParameter('order', $orden)
                    ->getQuery()
                    ->getResult();
            if($disenos != null){
              if(count($disenos) >= $orden->getCantidad()){
                $this->addFlash(
                    'error',
                    'La orden de diseño seleccionada ya cuenta con los diseños requeridos'
                );
                return $this->redirectToRoute('diseno_new');
              }
            }

            $diseno->setUsuarioCreacion($user);
            $diseno->setFechaCreacion($fecha);
            $diseno->setEstado(1);

            foreach($diseno->getMateriales() as &$material){
              $material->setEstado(1);
            }

            foreach($diseno->getImagenes() as &$imagen){
              $imagen->setEstado(1);
            }

            $em->persist($diseno);
            $em->flush($diseno);

            $proceso2 = new Proceso();
            $proceso2->setDiseno($diseno);
            $proceso2->setFechaInicio($fecha);
            $proceso2->setCantidad(1);
            $proceso2->setStatus(1);
            $proceso2->setProceso('DISEÑO');
            $proceso2->setTipoOrden('DISEÑO');
            $proceso2->setOrden($orden->getId());
            $proceso2->setUserCreacion($user);
            $em->persist($proceso2);
            $em->flush($proceso2);

            $novedad = new DisenoNovedad();
            $novedad->setFechaCreacion($fecha);
            $novedad->setTipo('CREADO');
            $novedad->setUsuarioCreacion($user);
            $novedad->setDiseno($diseno);
            $novedad->setRef1('DISEÑO');
            $novedad->setDescripcion('Diseño creado');
            $em->persist($novedad);
            $em->flush($novedad);

            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('diseno_show',['id'=>$diseno->getId()]);

        }

        return $this->render('diseno/new.html.twig', array(
            'diseno' => $diseno,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a diseno entity.
     *
     */
    public function showAction(Diseno $diseno)
    {
      $fecha = new \DateTime();
        $em = $this->getDoctrine()->getManager();
        $inventarioOrden = $em->getRepository('AppBundle:InventarioOrden')
                ->createQueryBuilder('a')
                ->where('a.departamentoSolicita = :depto')
                ->setParameter('depto', 'DESIGN')
                ->andWhere('a.ref1 = :ref1')
                ->setParameter('ref1', $diseno->getId())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
                ;
        $procesos = $em->getRepository('AppBundle:Proceso')
                ->createQueryBuilder('a')
                ->where('a.diseno = :design')
                ->setParameter('design', $diseno->getId())
                ->andWhere('a.tipoOrden = :order')
                ->setParameter('order', 'DISEÑO')
                ->getQuery()
                ->getResult();

        if($inventarioOrden){
          $items_orden = $em->getRepository('AppBundle:InventarioOrdenItem')
                  ->createQueryBuilder('a')
                  ->where('a.inventarioOrden = :order')
                  ->setParameter('order', $inventarioOrden->getId())
                  ->getQuery()
                  ->getResult();
        }
        else{
          $items_orden =null;
        }
        $proceso_actual= "";
        $proceso_siguiente=[];
        $responsables=[];
        if($procesos == null){
          $proceso_actual= "NA";
          array_push($proceso_siguiente,"NA");
        }
        else {
          $interval = [];
          foreach ($procesos as $tarea) {
            if($tarea->getStatus() == 1){
              $proceso_actual = $tarea;
              if ($proceso_actual->getProceso() == "CORTE") {
                array_push($proceso_siguiente,"CONFECCION");
              }
              else if ($proceso_actual->getProceso() == "TRAZO") {
                array_push($proceso_siguiente,"CORTE");
              }
              else if ($proceso_actual->getProceso() == "DISEÑO") {
                array_push($proceso_siguiente,"TRAZO");
              }
              else if ($proceso_actual->getProceso() == "BORDADO") {
                array_push($proceso_siguiente,"CONFECCION");
                array_push($proceso_siguiente,"CORTE");
                array_push($proceso_siguiente,"LAVANDERIA");
                array_push($proceso_siguiente,"PRETERMINADOS");
              }
              else if ($proceso_actual->getProceso() == "CONFECCION") {
                array_push($proceso_siguiente,"CORTE");
                array_push($proceso_siguiente,"LAVANDERIA");
                array_push($proceso_siguiente,"PRETERMINADOS");
              }
              else if ($proceso_actual->getProceso() == "LAVANDERIA") {
                array_push($proceso_siguiente,"TERMINADOS");
                array_push($proceso_siguiente,"PRETERMINADOS");
              }
              else if ($proceso_actual->getProceso() == "TERMINADOS") {
                array_push($proceso_siguiente,"FOTOS");
              }
              else if ($proceso_actual->getProceso() == "PRETERMINADOS") {
                array_push($proceso_siguiente,"LAVANDERIA");
                array_push($proceso_siguiente,"TERMINADOS");
              }
              else if ($proceso_actual->getProceso() == "FOTOS") {
                array_push($proceso_siguiente,"FIN");
              }
            }
            if($tarea->getStatus() == 2 && $tarea->getProceso() == "FOTOS"){
              $proceso_actual = $tarea;
              array_push($proceso_siguiente,"FIN");
            }
          }
          #$interval[] = abs($fecha->getTimestamp() - $tarea->getFechaFinalizacion()->getTimestamp());
          #asort($interval);
          #$closest = key($interval);
          #$ultimo_proceso = $procesos[$closest]->getProceso();
          #var_dump($proceso_actual);exit;
        }
        $asignacion = $em->getRepository('AppBundle:ProcesoEncargado')
                ->createQueryBuilder('a')
                ->join('a.proceso','p')
                ->join('a.personaAsignada','r')
                ->andwhere('a.diseno = :design')
                ->setParameter('design', $diseno->getId())
                ->getQuery()
                ->getResult();
        $personas = $em->getRepository('AppBundle:EquipoTrabajo')
                ->createQueryBuilder('a')
                ->where('a.area = :area')
                ->setParameter('area', (is_string($proceso_actual))?$proceso_actual:$proceso_actual->getProceso())
                ->getQuery()
                ->getResult();
        if($personas!=null){
          foreach ($personas as $persona) {
            array_push($responsables,$persona);
          }
        }

        $notas = $em->getRepository('AppBundle:ProcesoNotas')
            ->createQueryBuilder('a')
            ->join('a.proceso','p')
            ->join('p.diseno','d')
            ->where('p.diseno = :design')
            ->setParameter('design', $diseno->getId())
            ->getQuery()
            ->getResult();
        return $this->render('diseno/show.html.twig', array(
            'diseno' => $diseno,
            'inventarioOrden' => $inventarioOrden,
            'orden_items' => $items_orden,
            'procesos' => $procesos,
            'encargados' => $responsables,
            'asignacion' => $asignacion,
            'proceso_actual' => $proceso_actual,
            'notas'=>$notas,
            'siguiente_proceso' => $proceso_siguiente
        //            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function show2Action(Diseno $diseno, ProduccionOrden $produccionOrden)
    {
      $fecha = new \DateTime();
      $em = $this->getDoctrine()->getManager();
      $inventarioOrden = $em->getRepository('AppBundle:InventarioOrden')
              ->createQueryBuilder('a')
              ->where('a.departamentoSolicita = :depto')
              ->setParameter('depto', 'PRODUCCION')
              ->andWhere('a.ref1 = :ref1')
              ->setParameter('ref1', $diseno->getId())
              ->andWhere('a.ref3 = :order')
              ->setParameter('order', $produccionOrden->getId())
              ->setMaxResults(1)
              ->getQuery()
              ->getOneOrNullResult()
              ;
      $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
              ->createQueryBuilder('a')
              ->where('a.diseno = :design')
              ->setParameter('design', $diseno->getId())
              ->andWhere('a.ordenProduccion = :order')
              ->setParameter('order', $produccionOrden)
              ->setMaxResults(1)
              ->getQuery()
              ->getOneOrNullResult();
      $caja = $em->getRepository('AppBundle:ProduccionEmpaque')
              ->createQueryBuilder('a')
              ->where('a.ordenProduccion = :order')
              ->setParameter('order', $produccionOrden)
              ->orderBy("a.id", "DESC")
              ->setMaxResults(1)
              ->getQuery()
              ->getOneOrNullResult();
      $procesos = $em->getRepository('AppBundle:Proceso')
              ->createQueryBuilder('a')
              ->where('a.diseno = :design')
              ->setParameter('design', $diseno->getId())
              ->andWhere('a.tipoOrden = :production')
              ->setParameter('production', 'PRODUCCION')
              ->andWhere('a.orden = :order')
              ->setParameter('order', $produccionOrden->getId())
              ->getQuery()
              ->getResult();

      if($inventarioOrden){
        $items_orden = $em->getRepository('AppBundle:InventarioOrdenItem')
                ->createQueryBuilder('a')
                ->where('a.inventarioOrden = :order')
                ->setParameter('order', $inventarioOrden->getId())
                ->getQuery()
                ->getResult();
      }
      else{
        $items_orden =null;
      }
      $proceso_actual= "";
      $proceso_siguiente=[];
      $responsables=[];
      if($procesos == null){
        $proceso_actual= "NA";
        array_push($proceso_siguiente,"NA");
      }
      else {
        $interval = [];
        foreach ($procesos as $tarea) {
          if($tarea->getStatus() == 1){
            $proceso_actual = $tarea;
            if ($proceso_actual->getProceso() == "CORTE") {
              array_push($proceso_siguiente,"CONFECCION");
              #array_push($proceso_siguiente,"BORDADO");
            }
            else if ($proceso_actual->getProceso() == "TRAZO") {
              array_push($proceso_siguiente,"CORTE");
            }
            else if ($proceso_actual->getProceso() == "DISEÑO") {
              array_push($proceso_siguiente,"TRAZO");
            }
            else if ($proceso_actual->getProceso() == "BORDADO") {
              array_push($proceso_siguiente,"CONFECCION");
              #array_push($proceso_siguiente,"CORTE");
            }
            else if ($proceso_actual->getProceso() == "CONFECCION") {
              array_push($proceso_siguiente,"CORTE");
              array_push($proceso_siguiente,"LAVANDERIA");
              array_push($proceso_siguiente,"PRETERMINADOS");
              array_push($proceso_siguiente,"EMPAQUE");
            }
            else if ($proceso_actual->getProceso() == "LAVANDERIA") {
              array_push($proceso_siguiente,"TERMINADOS");
              array_push($proceso_siguiente,"PRETERMINADOS");
            }
            else if ($proceso_actual->getProceso() == "TERMINADOS") {
              array_push($proceso_siguiente,"EMPAQUE");
            }
            else if ($proceso_actual->getProceso() == "PRETERMINADOS") {
              array_push($proceso_siguiente,"LAVANDERIA");
              array_push($proceso_siguiente,"TERMINADOS");
            }
            else if ($proceso_actual->getProceso() == "EMPAQUE") {
              array_push($proceso_siguiente,"FIN");
            }
          }
          if($tarea->getStatus() == 2 && $tarea->getProceso() == "EMPAQUE"){
            $proceso_actual = $tarea;
            array_push($proceso_siguiente,"FIN");
          }
        }
        #$interval[] = abs($fecha->getTimestamp() - $tarea->getFechaFinalizacion()->getTimestamp());
        #asort($interval);
        #$closest = key($interval);
        #$ultimo_proceso = $procesos[$closest]->getProceso();
        #var_dump($proceso_actual);exit;
      }
      $asignacion = $em->getRepository('AppBundle:ProcesoEncargado')
              ->createQueryBuilder('a')
              ->join('a.proceso','p')
              ->join('a.personaAsignada','r')
              ->andwhere('p.orden = :order')
              ->setParameter('order', $produccionOrden->getId())
              ->andwhere('a.diseno = :design')
              ->setParameter('design', $diseno->getId())
              ->orderBy('a.id', 'DESC')
              ->getQuery()
              ->getResult();
      $personas = $em->getRepository('AppBundle:EquipoTrabajo')
              ->createQueryBuilder('a')
              ->where('a.area = :area')
              ->setParameter('area', (is_string($proceso_actual))?$proceso_actual:$proceso_actual->getProceso())
              ->getQuery()
              ->getResult();
      if($personas!=null){
        foreach ($personas as $persona) {
          array_push($responsables,$persona);
        }
      }
      $tallasP = $em->getRepository('AppBundle:ProduccionTalla')
              ->createQueryBuilder('a')
              ->where('a.diseno = :design')
              ->setParameter('design', $disenoP->getId())
              ->getQuery()
              ->getResult();
      $costos = $em->getRepository('AppBundle:ProduccionCosto')
              ->createQueryBuilder('a')
              ->join('a.proceso','p')
              ->where('a.ordenProduccion = :order')
              ->setParameter('order', $produccionOrden)
              ->andWhere('p.nombre = :process')
              ->setParameter('process', $proceso_actual->getProceso())
              ->getQuery()
              ->getResult();
      $notas = $em->getRepository('AppBundle:ProcesoNotas')
          ->createQueryBuilder('a')
          ->join('a.proceso','p')
          ->join('p.diseno','d')
          ->where('p.diseno = :design')
          ->setParameter('design', $diseno->getId())
          ->andWhere('p.orden = :order')
          ->setParameter('order', $produccionOrden->getId())
          ->getQuery()
          ->getResult();
      return $this->render('diseno/show2.html.twig', array(
          'diseno' => $diseno,
          'orden_produccion' => $produccionOrden,
          'inventarioOrden' => $inventarioOrden,
          'orden_items' => $items_orden,
          'procesos' => $procesos,
          'encargados' => $responsables,
          'asignacion' => $asignacion,
          'proceso_actual' => $proceso_actual,
          'tallas' => $tallasP,
          'notas'=>$notas,
          'costos'=>$costos,
          'ultima_caja'=> $caja,
          'siguiente_proceso' => $proceso_siguiente
      //            'delete_form' => $deleteForm->createView(),
      ));
    }

    public function show3Action(Diseno $diseno, ProduccionOrden $produccionOrden)
    {
      $fecha = new \DateTime();
        $em = $this->getDoctrine()->getManager();
        $inventarioOrden = $em->getRepository('AppBundle:InventarioOrden')
                ->createQueryBuilder('a')
                ->where('a.departamentoSolicita = :depto')
                ->setParameter('depto', 'PRODUCCION')
                ->andWhere('a.ref1 = :ref1')
                ->setParameter('ref1', $diseno->getId())
                ->andWhere('a.ref3 = :order')
                ->setParameter('order', $produccionOrden->getId())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
                ;
        $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                ->createQueryBuilder('a')
                ->where('a.diseno = :design')
                ->setParameter('design', $diseno->getId())
                ->andWhere('a.ordenProduccion = :order')
                ->setParameter('order', $produccionOrden)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        $procesos = $em->getRepository('AppBundle:Proceso')
                ->createQueryBuilder('a')
                ->where('a.diseno = :design')
                ->setParameter('design', $diseno->getId())
                ->andWhere('a.tipoOrden = :production')
                ->setParameter('production', 'PRODUCCION')
                ->andWhere('a.orden = :order')
                ->setParameter('order', $produccionOrden->getId())
                ->getQuery()
                ->getResult();

        if($inventarioOrden){
          $items_orden = $em->getRepository('AppBundle:InventarioOrdenItem')
                  ->createQueryBuilder('a')
                  ->where('a.inventarioOrden = :order')
                  ->setParameter('order', $inventarioOrden->getId())
                  ->getQuery()
                  ->getResult();
        }
        else{
          $items_orden =null;
        }
        $proceso_actual= "";
        $proceso_siguiente=[];
        $responsables=[];
        if($procesos == null){
          $proceso_actual= "NA";
          array_push($proceso_siguiente,"NA");
        }
        else {
          $interval = [];
          foreach ($procesos as $tarea) {
            if($tarea->getStatus() == 0){
              $proceso_actual = $tarea;
              if ($proceso_actual->getProceso() == "BORDADO") {
                array_push($proceso_siguiente,"CONFECCION");
                #array_push($proceso_siguiente,"CORTE");
              }
            }
          }
          #$interval[] = abs($fecha->getTimestamp() - $tarea->getFechaFinalizacion()->getTimestamp());
          #asort($interval);
          #$closest = key($interval);
          #$ultimo_proceso = $procesos[$closest]->getProceso();
          #var_dump($proceso_actual);exit;
        }
        $asignacion = $em->getRepository('AppBundle:ProcesoEncargado')
                ->createQueryBuilder('a')
                ->join('a.proceso','p')
                ->join('a.personaAsignada','r')
                ->andwhere('p.orden = :order')
                ->setParameter('order', $produccionOrden->getId())
                ->andwhere('a.diseno = :design')
                ->setParameter('design', $diseno->getId())
                ->orderBy('a.id', 'DESC')
                ->getQuery()
                ->getResult();
        $personas = $em->getRepository('AppBundle:EquipoTrabajo')
                ->createQueryBuilder('a')
                ->where('a.area = :area')
                ->setParameter('area', (is_string($proceso_actual))?$proceso_actual:$proceso_actual->getProceso())
                ->getQuery()
                ->getResult();
        if($personas!=null){
          foreach ($personas as $persona) {
            array_push($responsables,$persona);
          }
        }
        $tallasP = $em->getRepository('AppBundle:ProduccionTalla')
                ->createQueryBuilder('a')
                ->where('a.diseno = :design')
                ->setParameter('design', $disenoP->getId())
                ->getQuery()
                ->getResult();
        $costos = $em->getRepository('AppBundle:ProduccionCosto')
                ->createQueryBuilder('a')
                ->join('a.proceso','p')
                ->where('a.ordenProduccion = :order')
                ->setParameter('order', $produccionOrden)
                ->andWhere('p.nombre = :process')
                ->setParameter('process', $proceso_actual->getProceso())
                ->getQuery()
                ->getResult();
        $piezas = $em->getRepository('AppBundle:ProduccionBordado')
            ->createQueryBuilder('a')
            ->where('a.ordenProduccion = :order')
            ->setParameter('order', $produccionOrden)
            ->getQuery()
            ->getResult();
        $notas = $em->getRepository('AppBundle:ProcesoNotas')
            ->createQueryBuilder('a')
            ->join('a.proceso','p')
            ->join('p.diseno','d')
            ->where('p.diseno = :design')
            ->setParameter('design', $diseno->getId())
            ->andWhere('p.orden = :order')
            ->setParameter('order', $produccionOrden->getId())
            ->getQuery()
            ->getResult();
        return $this->render('diseno/show3.html.twig', array(
            'diseno' => $diseno,
            'orden_produccion' => $produccionOrden,
            'inventarioOrden' => $inventarioOrden,
            'orden_items' => $items_orden,
            'procesos' => $procesos,
            'encargados' => $responsables,
            'asignacion' => $asignacion,
            'proceso_actual' => $proceso_actual,
            'tallas' => $tallasP,
            'notas'=>$notas,
            'costos'=>$costos,
            'piezas' =>$piezas,
            'siguiente_proceso' => $proceso_siguiente
        //            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function show4Action(Diseno $diseno)
    {
      $fecha = new \DateTime();
        $em = $this->getDoctrine()->getManager();
        $inventarioOrden = $em->getRepository('AppBundle:InventarioOrden')
                ->createQueryBuilder('a')
                ->where('a.departamentoSolicita = :depto')
                ->setParameter('depto', 'DESIGN')
                ->andWhere('a.ref1 = :ref1')
                ->setParameter('ref1', $diseno->getId())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
                ;
        $procesos = $em->getRepository('AppBundle:Proceso')
                ->createQueryBuilder('a')
                ->where('a.diseno = :design')
                ->setParameter('design', $diseno->getId())
                ->andWhere('a.tipoOrden = :order')
                ->setParameter('order', 'DISEÑO')
                ->getQuery()
                ->getResult();

        if($inventarioOrden){
          $items_orden = $em->getRepository('AppBundle:InventarioOrdenItem')
                  ->createQueryBuilder('a')
                  ->where('a.inventarioOrden = :order')
                  ->setParameter('order', $inventarioOrden->getId())
                  ->getQuery()
                  ->getResult();
        }
        else{
          $items_orden =null;
        }
        $proceso_actual= "";
        $proceso_siguiente=[];
        $responsables=[];
        if($procesos == null){
          $proceso_actual= "NA";
          array_push($proceso_siguiente,"NA");
        }
        else {
          $interval = [];
          foreach ($procesos as $tarea) {
            if($tarea->getStatus() == 0){
              $proceso_actual = $tarea;
              if ($proceso_actual->getProceso() == "BORDADO") {
                array_push($proceso_siguiente,"CONFECCION");
              }
            }
          }
          #$interval[] = abs($fecha->getTimestamp() - $tarea->getFechaFinalizacion()->getTimestamp());
          #asort($interval);
          #$closest = key($interval);
          #$ultimo_proceso = $procesos[$closest]->getProceso();
          #var_dump($proceso_actual);exit;
        }
        $asignacion = $em->getRepository('AppBundle:ProcesoEncargado')
                ->createQueryBuilder('a')
                ->join('a.proceso','p')
                ->join('a.personaAsignada','r')
                ->andwhere('a.diseno = :design')
                ->setParameter('design', $diseno->getId())
                ->getQuery()
                ->getResult();
        $personas = $em->getRepository('AppBundle:EquipoTrabajo')
                ->createQueryBuilder('a')
                ->where('a.area = :area')
                ->setParameter('area', (is_string($proceso_actual))?$proceso_actual:$proceso_actual->getProceso())
                ->getQuery()
                ->getResult();
        if($personas!=null){
          foreach ($personas as $persona) {
            array_push($responsables,$persona);
          }
        }
        $piezas = $em->getRepository('AppBundle:DisenoBordado')
            ->createQueryBuilder('a')
            ->where('a.diseno = :design')
            ->setParameter('design', $diseno)
            ->getQuery()
            ->getResult();
        $notas = $em->getRepository('AppBundle:ProcesoNotas')
            ->createQueryBuilder('a')
            ->join('a.proceso','p')
            ->join('p.diseno','d')
            ->where('p.diseno = :design')
            ->setParameter('design', $diseno->getId())
            ->getQuery()
            ->getResult();
        return $this->render('diseno/show4.html.twig', array(
            'diseno' => $diseno,
            'inventarioOrden' => $inventarioOrden,
            'orden_items' => $items_orden,
            'procesos' => $procesos,
            'encargados' => $responsables,
            'asignacion' => $asignacion,
            'proceso_actual' => $proceso_actual,
            'notas'=>$notas,
            'piezas'=>$piezas,
            'siguiente_proceso' => $proceso_siguiente
        //            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing diseno entity.
     *
     */
    public function editAction(Request $request, Diseno $diseno)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if(!$user->hasRole('ROLE_SUPER_ADMIN')||!$user->hasRole('ROLE_SUPER_ADMIN')){
          if($diseno->getUsuarioCreacion()->getId() != $user->getId()){
            $this->addFlash(
                'error',
                'No tiene permisos para este registro'
            );
            return $this->redirectToRoute('diseno_index');
          }
        }

        $originalTags = new ArrayCollection();
        $originalImages = new ArrayCollection();
        foreach ($diseno->getMateriales() as $material) {$originalTags->add($material);}
        foreach ($diseno->getImagenes() as $imagen) {$originalImages->add($imagen);}

        $editForm = $this->createForm('AppBundle\Form\DisenoType', $diseno);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            foreach($diseno->getMateriales() as $material){$material->setEstado(1);}
            foreach($diseno->getImagenes() as $imagen){$imagen->setEstado(1);}

            foreach ($originalTags as $item) {
                if (false === $diseno->getMateriales()->contains($item)) {
                  $item->setDiseno(null);
                  $em->remove($item);
                  $em->flush();
                }
            }
            foreach ($originalImages as $item2) {
                if (false === $diseno->getImagenes()->contains($item2)) {
                  $item2->setDiseno(null);
                  $em->remove($item2);
                  $em->flush();
                }
            }

            $diseno->setEstado(1);
            $em->persist($diseno);

            $novedad = new DisenoNovedad();
            $novedad->setFechaCreacion($fecha);
            $novedad->setTipo('EDITADO');
            $novedad->setUsuarioCreacion($user);
            $novedad->setDiseno($diseno);
            $novedad->setDescripcion('Diseño editado');
            $em->persist($novedad);

            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('diseno_show',['id'=>$diseno->getId()]);
        }

        return $this->render('diseno/edit.html.twig', array(
            'diseno' => $diseno,
            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN_PRODUCCION')")
     *
     */
    public function aprobarAction(Request $request, Diseno $diseno)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        $diseno->setEstado(2);
        $em->persist($diseno);

        $novedad = new DisenoNovedad();
        $novedad->setFechaCreacion($fecha);
        $novedad->setTipo('APROBADO');
        $novedad->setUsuarioCreacion($user);
        $novedad->setDiseno($diseno);
        $novedad->setDescripcion('Diseño aprobado');
        $em->persist($novedad);

        $em->flush();
        $this->addFlash(
            'success',
            'Diseño aprobado correctamente'
        );
        return $this->redirectToRoute('diseno_show',['id'=>$diseno->getId()]);
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN_PRODUCCION')")
     *
     */
    public function rechazarAction(Request $request, Diseno $diseno)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        $diseno->setEstado(0);
        $em->persist($diseno);

        $novedad = new DisenoNovedad();
        $novedad->setFechaCreacion($fecha);
        $novedad->setTipo('RECHAZADO');
        $novedad->setUsuarioCreacion($user);
        $novedad->setDiseno($diseno);
        $novedad->setDescripcion('Diseño rechazado');
        $em->persist($novedad);

        $em->flush();
        $this->addFlash(
            'warning',
            'Diseño rechazado correctamente'
        );
        return $this->redirectToRoute('diseno_show',['id'=>$diseno->getId()]);
    }

    /**
     * Deletes a diseno entity.
     *
     */
//    public function deleteAction(Request $request, Diseno $diseno)
//    {
//        $form = $this->createDeleteForm($diseno);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($diseno);
//            $em->flush($diseno);
//        }
//
//        return $this->redirectToRoute('diseno_index');
//    }

    /**
     * Creates a form to delete a diseno entity.
     *
     * @param Diseno $diseno The diseno entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createDeleteForm(Diseno $diseno)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('diseno_delete', array('id' => $diseno->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }

//    public function eraseAction(Diseno $diseno)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $em->remove($diseno);
//        $em->flush($diseno);
//        $this->addFlash(
//            'success',
//            'Registro eliminado correctamente'
//        );
//        return $this->redirectToRoute('diseno_index');
//    }
}
