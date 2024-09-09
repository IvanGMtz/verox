<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use AppBundle\Entity\Diseno;
use AppBundle\Entity\DisenoNovedad;
use AppBundle\Entity\Proceso;
use AppBundle\Entity\ProcesoEncargado;
use AppBundle\Entity\ProduccionOrden;
use AppBundle\Entity\EquipoTrabajo;
use AppBundle\Entity\ProcesoNotas;
use AppBundle\Entity\InventarioOrden;
use AppBundle\Entity\ProduccionNovedad;
use AppBundle\Entity\ProduccionBordado;
use AppBundle\Entity\DisenoBordado;
use AppBundle\Entity\ProduccionEmpaque;
use AppBundle\Entity\DisenoOrden;
use AppBundle\Entity\InventarioOrdenItem;
use AppBundle\Entity\InventarioOrdenNovedad;
use AppBundle\Entity\ProduccionDiseno;

/**
 * Proceso controller.
 *
 */
class ProcesoController extends Controller
{
    /**
     * Lists all proceso entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.Proceso'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }

        $procesosQ = $em->getRepository('AppBundle:Proceso')->createQueryBuilder('a');

        if($q && $q !=''){
          $this->get('session')->set('q.Proceso', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $procesosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $procesosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }

        $query = $procesosQ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );

        $procesos = $pagination->getItems();


        return $this->render('proceso/index.html.twig', array(
            'procesos' => $procesos,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new proceso entity.
     *
     */
    public function newAction(Request $request)
    {
        $proceso = new Proceso();
        $form = $this->createForm('AppBundle\Form\ProcesoType', $proceso);
        $form->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($proceso);
            $em->flush($proceso);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('proceso_index');

        }

        return $this->render('proceso/new.html.twig', array(
            'proceso' => $proceso,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a proceso entity.
     *
     */
    public function showAction(Proceso $proceso)
    {
        $deleteForm = $this->createDeleteForm($proceso);

        return $this->render('proceso/show.html.twig', array(
            'proceso' => $proceso,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing proceso entity.
     *
     */
    public function editAction(Request $request, Proceso $proceso)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($proceso);
        $editForm = $this->createForm('AppBundle\Form\ProcesoType', $proceso);
        $editForm->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('proceso_index');
        }

        return $this->render('proceso/edit.html.twig', array(
            'proceso' => $proceso,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a proceso entity.
     *
     */
    public function deleteAction(Request $request, Proceso $proceso)
    {
        $form = $this->createDeleteForm($proceso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($proceso);
            $em->flush($proceso);
        }

        return $this->redirectToRoute('proceso_index');
    }

    /**
     * Creates a form to delete a proceso entity.
     *
     * @param Proceso $proceso The proceso entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Proceso $proceso)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('proceso_delete', array('id' => $proceso->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function eraseAction(Proceso $proceso)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($proceso);
        $em->flush($proceso);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('proceso_index');
    }

    public function pasarProcesoConfeccionAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $proceso->getFechaInicio();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );

      $proceso->setFechaFinalizacion($ahora);
      $proceso->setStatus(2);
      $proceso->setDuracion($duracion);

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = $proceso->getCantidad();
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if (strpos($key, 'CC-') !== false) {
          $id_pieza = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $pieza = $em->getRepository('AppBundle:ProduccionBordado')
                  ->createQueryBuilder('a')
                  ->where('a.id = :id')
                  ->setParameter('id', $id_pieza)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT -= ($pieza->getCantidadConfirmada() - (int)$request->request->get($key));
          $pieza->setCantidadConfirmada($request->request->get($key));
          $cambiosQ = true;
          $novedadP = new ProduccionNovedad();
          $novedadP->setOrdenProduccion($ordenProduccion);
          $novedadP->setDiseno($disenoP);
          $novedadP->setNovedad('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la pieza '.$pieza->getTipo().' del diseño: '.$diseno->getNombre());
          $novedadP->setTipo('Cantidad modificada');
          $novedadP->setFechaCreacion($ahora);
          $em->persist($novedadP);
          $em->flush();

          if($request->request->get("notas") != ""){
            $notas = new ProcesoNotas();
            $notas->setProceso($proceso);
            $notas->setNotas($request->request->get("notas"));
            $notas->setUsuario($user);
            $em->persist($notas);
            $em->flush();
          }

          $registroP = new DisenoNovedad();
          $registroP->setDiseno($diseno);
          $registroP->setUsuarioCreacion($user);
          $registroP->setFechaCreacion($ahora);
          $registroP->setDescripcion('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor de '.$pieza->getTipo());
          $registroP->setRef1($request->request->get('tipo_orden'));
          $registroP->setRef2($ordenProduccion->getId());
          $registroP->setTipo($proceso->getProceso());

          $em->persist($registroP);
          $em->flush();
        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }
      $registro1 = new DisenoNovedad();
      $registro1->setDiseno($diseno);
      $registro1->setUsuarioCreacion($user);
      $registro1->setFechaCreacion($ahora);
      $registro1->setDescripcion('Proceso de '.$proceso->getProceso().' terminado');
      $registro1->setTipo($proceso->getProceso());
      $registro1->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro1->setRef2($ordenProduccion->getId());
      }
      $em->persist($registro1);
      $em->flush();
      if($proceso->getProceso()!="BORDADO" && $request->request->get('tipo_orden') == "PRODUCCION"){
        $proceso2 = new Proceso();
        $proceso2->setDiseno($diseno);
        $proceso2->setFechaInicio($ahora);
        $proceso2->setCantidad($proceso->getCantidad());
        $proceso2->setStatus(1);
        $proceso2->setTipoOrden($request->request->get('tipo_orden'));
        $proceso2->setOrden($ordenProduccion->getId());
        $proceso2->setProceso('CONFECCION');
        $proceso2->setUserCreacion($user);

        $em->persist($proceso2);
        $em->flush();

        $registro = new DisenoNovedad();
        $registro->setDiseno($diseno);
        $registro->setUsuarioCreacion($user);
        $registro->setFechaCreacion($ahora);
        $registro->setDescripcion('Proceso de CONFECCIÓN Iniciado');
        $registro->setRef1($request->request->get('tipo_orden'));
        $registro->setRef2($ordenProduccion->getId());
        $registro->setTipo('CONFECCION');

        $em->persist($registro);
        $em->flush();
      }
      else if ($proceso->getProceso()=="BORDADO" && $request->request->get('tipo_orden') == "PRODUCCION") {
        $piezas = $em->getRepository('AppBundle:ProduccionBordado')
                ->createQueryBuilder('a')
                ->where('a.ordenProduccion = :orden')
                ->setParameter('orden', $ordenProduccion)
                ->andWhere('a.estado = :status')
                ->setParameter('status', 0)
                ->getQuery()
                ->getResult();
        foreach ($piezas as $key => $item) {
          $item->setEstado(1);
        }
      }
      else if($proceso->getProceso()!="BORDADO" && $request->request->get('tipo_orden') == "DISEÑO"){
        $proceso2 = new Proceso();
        $proceso2->setDiseno($diseno);
        $proceso2->setFechaInicio($ahora);
        $proceso2->setCantidad($proceso->getCantidad());
        $proceso2->setStatus(1);
        $proceso2->setTipoOrden($request->request->get('tipo_orden'));
        $proceso2->setOrden($diseno->getOrden()->getId());
        $proceso2->setProceso('CONFECCION');
        $proceso2->setUserCreacion($user);

        $em->persist($proceso2);
        $em->flush();

        $registro = new DisenoNovedad();
        $registro->setDiseno($diseno);
        $registro->setUsuarioCreacion($user);
        $registro->setFechaCreacion($ahora);
        $registro->setDescripcion('Proceso de CONFECCIÓN Iniciado');
        $registro->setRef1($request->request->get('tipo_orden'));
        $registro->setTipo('CONFECCION');

        $em->persist($registro);
        $em->flush();
      }
      else if($proceso->getProceso()=="BORDADO" && $request->request->get('tipo_orden') == "DISEÑO"){
        $piezas = $em->getRepository('AppBundle:DisenoBordado')
                ->createQueryBuilder('a')
                ->where('a.diseno = :design')
                ->setParameter('design', $diseno)
                ->andWhere('a.estado = :status')
                ->setParameter('status', 0)
                ->getQuery()
                ->getResult();
        foreach ($piezas as $key => $item) {
          $item->setEstado(1);
        }
      }
      $this->addFlash(
        'success',
        'Proceso terminado correctamente'
      );
      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function pasarProcesoLavanderiaAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $proceso->getFechaInicio();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );

      $proceso->setFechaFinalizacion($ahora);
      $proceso->setStatus(2);
      $proceso->setDuracion($duracion);

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = $proceso->getCantidad();
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
        if($proceso->getProceso() == "CONFECCION"){
          $design_production = $this->getDoctrine()->getRepository(ProduccionDiseno::class)->findBy(['diseno' => $diseno,'ordenProduccion'=>$ordenProduccion]);
          foreach ($params as $key => $value) {
            if ($key == "puntadas") {
              $design_production[0]->setPuntadas($value);
            }
          }
        }
      }
      foreach ($params as $key => $param) {
        if (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT -= ($tallaP->getCantidadConfirmada() - (int)$request->request->get($key));
          $tallaP->setCantidadConfirmada($request->request->get($key));
          $cambiosQ = true;
          $novedadP = new ProduccionNovedad();
          $novedadP->setOrdenProduccion($ordenProduccion);
          $novedadP->setDiseno($disenoP);
          $novedadP->setNovedad('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla.' del diseño: '.$diseno->getNombre());
          $novedadP->setTipo('Cantidad de producción modificada');
          $novedadP->setFechaCreacion($ahora);
          $em->persist($novedadP);
          $em->flush();

          if($request->request->get("notas") != ""){
            $notas = new ProcesoNotas();
            $notas->setProceso($proceso);
            $notas->setNotas($request->request->get("notas"));
            $notas->setUsuario($user);
            $em->persist($notas);
            $em->flush();
          }

          $registroP = new DisenoNovedad();
          $registroP->setDiseno($diseno);
          $registroP->setUsuarioCreacion($user);
          $registroP->setFechaCreacion($ahora);
          $registroP->setDescripcion('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla);
          $registroP->setRef1($request->request->get('tipo_orden'));
          $registroP->setRef2($ordenProduccion->getId());
          $registroP->setTipo($proceso->getProceso());

          $em->persist($registroP);
          $em->flush();
        }
        if ($key=="presillas_qty"){
          $diseno->setPresillas($param);
        }
        if ($key=="ojales_qty"){
          $diseno->setOjales($param);
        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }

      $registro1 = new DisenoNovedad();
      $registro1->setDiseno($diseno);
      $registro1->setUsuarioCreacion($user);
      $registro1->setFechaCreacion($ahora);
      $registro1->setDescripcion('Proceso de '.$proceso->getProceso().' terminado');
      $registro1->setTipo($proceso->getProceso());
      $registro1->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro1->setRef2($ordenProduccion->getId());
      }
      $em->persist($registro1);
      $em->flush();

      $proceso2 = new Proceso();
      $proceso2->setDiseno($diseno);
      $proceso2->setFechaInicio($ahora);
      $proceso2->setCantidad($proceso->getCantidad());
      $proceso2->setStatus(1);
      $proceso2->setTipoOrden($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $proceso2->setOrden($ordenProduccion->getId());
      }
      else{
        $proceso2->setOrden($diseno->getOrden()->getId());
      }
      $proceso2->setProceso('LAVANDERIA');
      $proceso2->setUserCreacion($user);

      $em->persist($proceso2);
      $em->flush();

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Proceso de LAVANDERÍA Iniciado');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('LAVANDERIA');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Proceso terminado correctamente'
      );
      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function pasarProcesoPreterminadosAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $proceso->getFechaInicio();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );

      $proceso->setFechaFinalizacion($ahora);
      $proceso->setStatus(2);
      $proceso->setDuracion($duracion);

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = $proceso->getCantidad();
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
        if($proceso->getProceso() == "CONFECCION"){
          $design_production = $this->getDoctrine()->getRepository(ProduccionDiseno::class)->findBy(['diseno' => $diseno,'ordenProduccion'=>$ordenProduccion]);
          foreach ($params as $key => $value) {
            if ($key == "puntadas") {
              $design_production[0]->setPuntadas($value);
            }
          }
        }
      }
      foreach ($params as $key => $param) {
        if (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT -= ($tallaP->getCantidadConfirmada() - (int)$request->request->get($key));
          $tallaP->setCantidadConfirmada($request->request->get($key));
          $cambiosQ = true;
          $novedadP = new ProduccionNovedad();
          $novedadP->setOrdenProduccion($ordenProduccion);
          $novedadP->setDiseno($disenoP);
          $novedadP->setNovedad('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla.' del diseño: '.$diseno->getNombre());
          $novedadP->setTipo('Cantidad de producción modificada');
          $novedadP->setFechaCreacion($ahora);
          $em->persist($novedadP);
          $em->flush();

          if($request->request->get("notas") != ""){
            $notas = new ProcesoNotas();
            $notas->setProceso($proceso);
            $notas->setNotas($request->request->get("notas"));
            $notas->setUsuario($user);
            $em->persist($notas);
            $em->flush();
          }

          $registroP = new DisenoNovedad();
          $registroP->setDiseno($diseno);
          $registroP->setUsuarioCreacion($user);
          $registroP->setFechaCreacion($ahora);
          $registroP->setDescripcion('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla);
          $registroP->setRef1($request->request->get('tipo_orden'));
          $registroP->setRef2($ordenProduccion->getId());
          $registroP->setTipo($proceso->getProceso());

          $em->persist($registroP);
          $em->flush();
        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }

      $registro1 = new DisenoNovedad();
      $registro1->setDiseno($diseno);
      $registro1->setUsuarioCreacion($user);
      $registro1->setFechaCreacion($ahora);
      $registro1->setDescripcion('Proceso de '.$proceso->getProceso().' terminado');
      $registro1->setTipo($proceso->getProceso());
      $registro1->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro1->setRef2($ordenProduccion->getId());
      }
      $em->persist($registro1);
      $em->flush();

      $proceso2 = new Proceso();
      $proceso2->setDiseno($diseno);
      $proceso2->setFechaInicio($ahora);
      $proceso2->setCantidad($proceso->getCantidad());
      $proceso2->setStatus(1);
      $proceso2->setTipoOrden($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $proceso2->setOrden($ordenProduccion->getId());
      }
      else{
        $proceso2->setOrden($diseno->getOrden()->getId());
      }
      $proceso2->setProceso('PRETERMINADOS');
      $proceso2->setUserCreacion($user);

      $em->persist($proceso2);
      $em->flush();

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Proceso de PRETERMINADOS Iniciado');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('PRETERMINADOS');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Proceso terminado correctamente'
      );
      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function pasarProcesoTerminadosAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $proceso->getFechaInicio();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );

      $proceso->setFechaFinalizacion($ahora);
      $proceso->setStatus(2);
      $proceso->setDuracion($duracion);

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = $proceso->getCantidad();
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT -= ($tallaP->getCantidadConfirmada() - (int)$request->request->get($key));
          $tallaP->setCantidadConfirmada($request->request->get($key));
          $cambiosQ = true;
          $novedadP = new ProduccionNovedad();
          $novedadP->setOrdenProduccion($ordenProduccion);
          $novedadP->setDiseno($disenoP);
          $novedadP->setNovedad('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla.' del diseño: '.$diseno->getNombre());
          $novedadP->setTipo('Cantidad de producción modificada');
          $novedadP->setFechaCreacion($ahora);
          $em->persist($novedadP);
          $em->flush();

          if($request->request->get("notas") != ""){
            $notas = new ProcesoNotas();
            $notas->setProceso($proceso);
            $notas->setNotas($request->request->get("notas"));
            $notas->setUsuario($user);
            $em->persist($notas);
            $em->flush();
          }

          $registroP = new DisenoNovedad();
          $registroP->setDiseno($diseno);
          $registroP->setUsuarioCreacion($user);
          $registroP->setFechaCreacion($ahora);
          $registroP->setDescripcion('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla);
          $registroP->setRef1($request->request->get('tipo_orden'));
          $registroP->setRef2($ordenProduccion->getId());
          $registroP->setTipo($proceso->getProceso());

          $em->persist($registroP);
          $em->flush();
        }
        if ($key=="presillas_qty"){
          $diseno->setPresillas($param);
        }
        if ($key=="ojales_qty"){
          $diseno->setOjales($param);
        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }

      $registro1 = new DisenoNovedad();
      $registro1->setDiseno($diseno);
      $registro1->setUsuarioCreacion($user);
      $registro1->setFechaCreacion($ahora);
      $registro1->setDescripcion('Proceso de '.$proceso->getProceso().' terminado');
      $registro1->setTipo($proceso->getProceso());
      $registro1->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro1->setRef2($ordenProduccion->getId());
      }
      $em->persist($registro1);
      $em->flush();

      $proceso2 = new Proceso();
      $proceso2->setDiseno($diseno);
      $proceso2->setFechaInicio($ahora);
      $proceso2->setCantidad($proceso->getCantidad());
      $proceso2->setStatus(1);
      $proceso2->setTipoOrden($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $proceso2->setOrden($ordenProduccion->getId());
      }
      else{
        $proceso2->setOrden($diseno->getOrden()->getId());
      }
      $proceso2->setProceso('TERMINADOS');
      $proceso2->setUserCreacion($user);

      $em->persist($proceso2);
      $em->flush();

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Proceso de TERMINADOS Iniciado');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('TERMINADOS');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Proceso terminado correctamente'
      );
      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function pasarProcesoEmpaqueAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $proceso->getFechaInicio();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );

      $proceso->setFechaFinalizacion($ahora);
      $proceso->setStatus(2);
      $proceso->setDuracion($duracion);

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = $proceso->getCantidad();
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
        if($proceso->getProceso() == "CONFECCION"){
          $design_production = $this->getDoctrine()->getRepository(ProduccionDiseno::class)->findBy(['diseno' => $diseno,'ordenProduccion'=>$ordenProduccion]);
          foreach ($params as $key => $value) {
            if ($key == "puntadas") {
              $design_production[0]->setPuntadas($value);
            }
          }
        }
      }
      foreach ($params as $key => $param) {
        if (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT -= ($tallaP->getCantidadConfirmada() - (int)$request->request->get($key));
          $tallaP->setCantidadConfirmada($request->request->get($key));
          $cambiosQ = true;
          $novedadP = new ProduccionNovedad();
          $novedadP->setOrdenProduccion($ordenProduccion);
          $novedadP->setDiseno($disenoP);
          $novedadP->setNovedad('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla.' del diseño: '.$diseno->getNombre());
          $novedadP->setTipo('Cantidad de producción modificada');
          $novedadP->setFechaCreacion($ahora);
          $em->persist($novedadP);
          $em->flush();

          if($request->request->get("notas") != ""){
            $notas = new ProcesoNotas();
            $notas->setProceso($proceso);
            $notas->setNotas($request->request->get("notas"));
            $notas->setUsuario($user);
            $em->persist($notas);
            $em->flush();
          }

          $registroP = new DisenoNovedad();
          $registroP->setDiseno($diseno);
          $registroP->setUsuarioCreacion($user);
          $registroP->setFechaCreacion($ahora);
          $registroP->setDescripcion('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla);
          $registroP->setRef1($request->request->get('tipo_orden'));
          $registroP->setRef2($ordenProduccion->getId());
          $registroP->setTipo($proceso->getProceso());

          $em->persist($registroP);
          $em->flush();
        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }

      $registro1 = new DisenoNovedad();
      $registro1->setDiseno($diseno);
      $registro1->setUsuarioCreacion($user);
      $registro1->setFechaCreacion($ahora);
      $registro1->setDescripcion('Proceso de '.$proceso->getProceso().' terminado');
      $registro1->setTipo($proceso->getProceso());
      $registro1->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro1->setRef2($ordenProduccion->getId());
      }
      $em->persist($registro1);
      $em->flush();

      $proceso2 = new Proceso();
      $proceso2->setDiseno($diseno);
      $proceso2->setFechaInicio($ahora);
      $proceso2->setCantidad($proceso->getCantidad());
      $proceso2->setStatus(1);
      $proceso2->setTipoOrden($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $proceso2->setOrden($ordenProduccion->getId());
      }
      else{
        $proceso2->setOrden($diseno->getOrden()->getId());
      }
      $proceso2->setProceso('EMPAQUE');
      $proceso2->setUserCreacion($user);

      $em->persist($proceso2);
      $em->flush();

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Proceso de EMPAQUE Iniciado');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('EMPAQUE');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Proceso terminado correctamente'
      );
      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function pasarProcesoTrazoAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $proceso->getFechaInicio();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );

      $proceso->setFechaFinalizacion($ahora);
      $proceso->setStatus(2);
      $proceso->setDuracion($duracion);

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = $proceso->getCantidad();
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT -= ($tallaP->getCantidadConfirmada() - (int)$request->request->get($key));
          $tallaP->setCantidadConfirmada($request->request->get($key));
          $cambiosQ = true;
          $novedadP = new ProduccionNovedad();
          $novedadP->setOrdenProduccion($ordenProduccion);
          $novedadP->setDiseno($disenoP);
          $novedadP->setNovedad('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla.' del diseño: '.$diseno->getNombre());
          $novedadP->setTipo('Cantidad de producción modificada');
          $novedadP->setFechaCreacion($ahora);
          $em->persist($novedadP);
          $em->flush();
          if($request->request->get("notas") != ""){
            $notas = new ProcesoNotas();
            $notas->setProceso($proceso);
            $notas->setNotas($request->request->get("notas"));
            $notas->setUsuario($user);
            $em->persist($notas);
            $em->flush();
          }

          $registroP = new DisenoNovedad();
          $registroP->setDiseno($diseno);
          $registroP->setUsuarioCreacion($user);
          $registroP->setFechaCreacion($ahora);
          $registroP->setDescripcion('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla);
          $registroP->setRef1($request->request->get('tipo_orden'));
          $registroP->setRef2($ordenProduccion->getId());
          $registroP->setTipo($proceso->getProceso());

          $em->persist($registroP);
          $em->flush();
        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }

      $registro1 = new DisenoNovedad();
      $registro1->setDiseno($diseno);
      $registro1->setUsuarioCreacion($user);
      $registro1->setFechaCreacion($ahora);
      $registro1->setDescripcion('Proceso de '.$proceso->getProceso().' terminado');
      $registro1->setTipo($proceso->getProceso());
      $registro1->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro1->setRef2($ordenProduccion->getId());
      }
      $em->persist($registro1);
      $em->flush();

      $proceso2 = new Proceso();
      $proceso2->setDiseno($diseno);
      $proceso2->setFechaInicio($ahora);
      $proceso2->setCantidad($proceso->getCantidad());
      $proceso2->setStatus(1);
      $proceso2->setTipoOrden($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $proceso2->setOrden($ordenProduccion->getId());
      }
      else{
        $proceso2->setOrden($diseno->getOrden()->getId());
      }
      $proceso2->setProceso('TRAZO');
      $proceso2->setUserCreacion($user);

      $em->persist($proceso2);
      $em->flush();

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Proceso de TRAZO Iniciado');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('TRAZO');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Proceso terminado correctamente'
      );
      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function pasarProcesoBordadoAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $proceso->getFechaInicio();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );
      $params = $request->request->all();
      $qty=0;
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
        foreach ($params as $key => $param) {
          if (strpos($key, 'Q-') !== false) {
            $index = explode("-",$key)[1];
            $bordado = new ProduccionBordado();
            $bordado->setOrdenProduccion($ordenProduccion);
            $bordado->setCantidad($request->request->get($key));
            $bordado->setEstado(0);
            $bordado->setCantidadConfirmada($request->request->get($key));
            $bordado->setTipo($request->request->get('tipo-'.$index));
            $bordado->setFechaCreacion($ahora);
            $em->persist($bordado);
            $em->flush();

            if($request->request->get("notas") != ""){
              $notas = new ProcesoNotas();
              $notas->setProceso($proceso);
              $notas->setNotas($request->request->get("notas"));
              $notas->setUsuario($user);
              $em->persist($notas);
              $em->flush();
            }
            $qty += (int)$request->request->get($key);
          }
        }
      }
      else{
        foreach ($params as $key => $param) {
          if (strpos($key, 'Q-') !== false) {
            $index = explode("-",$key)[1];
            $bordado = new DisenoBordado();
            $bordado->setDiseno($diseno);
            $bordado->setCantidad($request->request->get($key));
            $bordado->setEstado(0);
            $bordado->setTipo($request->request->get('tipo-'.$index));
            $bordado->setFechaCreacion($ahora);
            $em->persist($bordado);
            $em->flush();

            if($request->request->get("notas") != ""){
              $notas = new ProcesoNotas();
              $notas->setProceso($proceso);
              $notas->setNotas($request->request->get("notas"));
              $notas->setUsuario($user);
              $em->persist($notas);
              $em->flush();
            }
            $qty += (int)$request->request->get($key);
          }
        }
      }
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $proceso2 = new Proceso();
        $proceso2->setDiseno($diseno);
        $proceso2->setFechaInicio($ahora);
        $proceso2->setCantidad($qty);
        $proceso2->setStatus(0);
        $proceso2->setTipoOrden($request->request->get('tipo_orden'));
        $proceso2->setOrden($ordenProduccion->getId());
        $proceso2->setProceso('BORDADO');
        $proceso2->setUserCreacion($user);

        $em->persist($proceso2);
        $em->flush();
      }
      else{
        #$proceso->setFechaFinalizacion($ahora);
        #$proceso->setStatus(2);
        #$proceso->setDuracion($duracion);
        $proceso2 = new Proceso();
        $proceso2->setDiseno($diseno);
        $proceso2->setFechaInicio($ahora);
        $proceso2->setCantidad(1);
        $proceso2->setStatus(0);
        $proceso2->setTipoOrden($request->request->get('tipo_orden'));
        $proceso2->setOrden($diseno->getOrden()->getId());
        $proceso2->setProceso('BORDADO');
        $proceso2->setUserCreacion($user);

        $em->persist($proceso2);
        $em->flush();
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Proceso de BORDADO Iniciado');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('BORDADO');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Proceso Asignado correctamente'
      );
      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function pasarProcesoCorteAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $proceso->getFechaInicio();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );
      $proceso->setFechaFinalizacion($ahora);
      $proceso->setStatus(2);
      $proceso->setDuracion($duracion);

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = $proceso->getCantidad();
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
        if($proceso->getProceso() == "CONFECCION"){
          $design_production = $this->getDoctrine()->getRepository(ProduccionDiseno::class)->findBy(['diseno' => $diseno,'ordenProduccion'=>$ordenProduccion]);
          foreach ($params as $key => $value) {
            if ($key == "puntadas") {
              $design_production[0]->setPuntadas($value);
            }
          }
        }
      }
      foreach ($params as $key => $param) {
        if (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT -= ($tallaP->getCantidadConfirmada() - (int)$request->request->get($key));
          $tallaP->setCantidadConfirmada($request->request->get($key));
          $cambiosQ = true;
          $novedadP = new ProduccionNovedad();
          $novedadP->setOrdenProduccion($ordenProduccion);
          $novedadP->setDiseno($disenoP);
          $novedadP->setNovedad('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla.' del diseño: '.$diseno->getNombre());
          $novedadP->setTipo('Cantidad de producción modificada');
          $novedadP->setFechaCreacion($ahora);
          $em->persist($novedadP);
          $em->flush();

          if($request->request->get("notas") != ""){
            $notas = new ProcesoNotas();
            $notas->setProceso($proceso);
            $notas->setNotas($request->request->get("notas"));
            $notas->setUsuario($user);
            $em->persist($notas);
            $em->flush();
          }

          $registroP = new DisenoNovedad();
          $registroP->setDiseno($diseno);
          $registroP->setUsuarioCreacion($user);
          $registroP->setFechaCreacion($ahora);
          $registroP->setDescripcion('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla);
          $registroP->setRef1($request->request->get('tipo_orden'));
          $registroP->setRef2($ordenProduccion->getId());
          $registroP->setTipo($proceso->getProceso());

          $em->persist($registroP);
          $em->flush();
        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }

      $registro1 = new DisenoNovedad();
      $registro1->setDiseno($diseno);
      $registro1->setUsuarioCreacion($user);
      $registro1->setFechaCreacion($ahora);
      $registro1->setDescripcion('Proceso de '.$proceso->getProceso().' terminado');
      $registro1->setTipo($proceso->getProceso());
      $registro1->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro1->setRef2($ordenProduccion->getId());
      }
      $em->persist($registro1);
      $em->flush();

      $proceso2 = new Proceso();
      $proceso2->setDiseno($diseno);
      $proceso2->setFechaInicio($ahora);
      $proceso2->setCantidad($proceso->getCantidad());
      $proceso2->setStatus(1);
      $proceso2->setTipoOrden($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $proceso2->setOrden($ordenProduccion->getId());
      }
      else{
        $proceso2->setOrden($diseno->getOrden()->getId());
      }
      $proceso2->setProceso('CORTE');
      $proceso2->setUserCreacion($user);

      $em->persist($proceso2);
      $em->flush();

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Proceso de CORTE Iniciado');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('CORTE');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Proceso terminado correctamente'
      );
      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function pasarProcesoFotosAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $proceso->getFechaInicio();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );

      $proceso->setFechaFinalizacion($ahora);
      $proceso->setStatus(2);
      $proceso->setDuracion($duracion);

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = $proceso->getCantidad();
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT -= ($tallaP->getCantidadConfirmada() - (int)$request->request->get($key));
          $tallaP->setCantidadConfirmada($request->request->get($key));
          $cambiosQ = true;
          $novedadP = new ProduccionNovedad();
          $novedadP->setOrdenProduccion($ordenProduccion);
          $novedadP->setDiseno($disenoP);
          $novedadP->setNovedad('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla.' del diseño: '.$diseno->getNombre());
          $novedadP->setTipo('Cantidad de producción modificada');
          $novedadP->setFechaCreacion($ahora);
          $em->persist($novedadP);
          $em->flush();

          if($request->request->get("notas") != ""){
            $notas = new ProcesoNotas();
            $notas->setProceso($proceso);
            $notas->setNotas($request->request->get("notas"));
            $notas->setUsuario($user);
            $em->persist($notas);
            $em->flush();
          }

          $registroP = new DisenoNovedad();
          $registroP->setDiseno($diseno);
          $registroP->setUsuarioCreacion($user);
          $registroP->setFechaCreacion($ahora);
          $registroP->setDescripcion('En el proceso de '.$proceso->getProceso().' se ha confirmado una cantidad menor para la talla '.$talla);
          $registroP->setRef1($request->request->get('tipo_orden'));
          $registroP->setRef2($ordenProduccion->getId());
          $registroP->setTipo($proceso->getProceso());

          $em->persist($registroP);
          $em->flush();
        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }

      $registro1 = new DisenoNovedad();
      $registro1->setDiseno($diseno);
      $registro1->setUsuarioCreacion($user);
      $registro1->setFechaCreacion($ahora);
      $registro1->setDescripcion('Proceso de '.$proceso->getProceso().' terminado');
      $registro1->setTipo($proceso->getProceso());
      $registro1->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro1->setRef2($ordenProduccion->getId());
      }
      $em->persist($registro1);
      $em->flush();

      $proceso2 = new Proceso();
      $proceso2->setDiseno($diseno);
      $proceso2->setFechaInicio($ahora);
      $proceso2->setCantidad($proceso->getCantidad());
      $proceso2->setStatus(1);
      $proceso2->setTipoOrden($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $proceso2->setOrden($ordenProduccion->getId());
      }
      else{
        $proceso2->setOrden($diseno->getOrden()->getId());
      }
      $proceso2->setProceso('FOTOS');
      $proceso2->setUserCreacion($user);

      $em->persist($proceso2);
      $em->flush();

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Proceso de FOTOS Iniciado');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('FOTOS');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Proceso terminado correctamente'
      );
      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function asignarProcesoTrazoAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      #var_dump($request->request);exit;
      $personas = $em->getRepository('AppBundle:EquipoTrabajo')
              ->createQueryBuilder('a')
              ->where('a.area = :area')
              ->setParameter('area', 'TRAZO')
              ->getQuery()
              ->getResult();

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = 0;
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if(strpos($key, 'Q-') !== false){
          $index = explode("-",$key)[1];
          $persona_encargada = $this->getDoctrine()->getRepository(EquipoTrabajo::class)->find($request->request->get('P-'.$index));
          $encargado = new ProcesoEncargado();
          $encargado->setProceso($proceso);
          $encargado->setPersonaAsignada($persona_encargada);
          $encargado->setDiseno($diseno);
          $encargado->setCantidad($request->request->get('Q-'.$index));
          $encargado->setTalla($request->request->get('T-'.$index));
          $encargado->setEnProceso(true);
          $encargado->setFechaAsignacion($ahora);
          $em->persist($encargado);
          $em->flush();
        }
        elseif (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT += (int)$request->request->get($key);
          if($tallaP->getCantidadConfirmada() > $request->request->get($key)){
            $tallaP->setCantidadConfirmada($request->request->get($key));
            $cambiosQ = true;
            $novedadP = new ProduccionNovedad();
            $novedadP->setOrdenProduccion($ordenProduccion);
            $novedadP->setDiseno($disenoP);
            $novedadP->setNovedad('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $novedadP->setTipo('Cantidad de producción modificada');
            $novedadP->setFechaCreacion($ahora);
            $em->persist($novedadP);
            $em->flush();

            $registroP = new DisenoNovedad();
            $registroP->setDiseno($diseno);
            $registroP->setUsuarioCreacion($user);
            $registroP->setFechaCreacion($ahora);
            $registroP->setDescripcion('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $registroP->setRef1($request->request->get('tipo_orden'));
            $registroP->setRef2($ordenProduccion->getId());
            $registroP->setTipo($proceso->getProceso());

            $em->persist($registroP);
            $em->flush();
          }

        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }
      if($request->request->get("notas") != ""){
        $notas = new ProcesoNotas();
        $notas->setProceso($proceso);
        $notas->setNotas($request->request->get("notas"));
        $notas->setUsuario($user);
        $em->persist($notas);
        $em->flush();
      }
      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Asignación de trabajo en proceso de TRAZO');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('TRAZO');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Asignación realizada correctamente'
      );

      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);

    }

    public function asignarProcesoCorteAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      #var_dump($request->request);exit;
      $personas = $em->getRepository('AppBundle:EquipoTrabajo')
              ->createQueryBuilder('a')
              ->where('a.area = :area')
              ->setParameter('area', 'CORTE')
              ->getQuery()
              ->getResult();

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = 0;
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if(strpos($key, 'Q-') !== false){
          $index = explode("-",$key)[1];
          $persona_encargada = $this->getDoctrine()->getRepository(EquipoTrabajo::class)->find($request->request->get('P-'.$index));
          $encargado = new ProcesoEncargado();
          $encargado->setProceso($proceso);
          $encargado->setPersonaAsignada($persona_encargada);
          $encargado->setDiseno($diseno);
          $encargado->setCantidad($request->request->get('Q-'.$index));
          $encargado->setTalla($request->request->get('T-'.$index));
          $encargado->setEnProceso(true);
          $encargado->setFechaAsignacion($ahora);
          $em->persist($encargado);
          $em->flush();
        }
        elseif (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT += (int)$request->request->get($key);
          if($tallaP->getCantidadConfirmada() > $request->request->get($key)){
            $tallaP->setCantidadConfirmada($request->request->get($key));
            $cambiosQ = true;
            $novedadP = new ProduccionNovedad();
            $novedadP->setOrdenProduccion($ordenProduccion);
            $novedadP->setDiseno($disenoP);
            $novedadP->setNovedad('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $novedadP->setTipo('Cantidad de producción modificada');
            $novedadP->setFechaCreacion($ahora);
            $em->persist($novedadP);
            $em->flush();

            $registroP = new DisenoNovedad();
            $registroP->setDiseno($diseno);
            $registroP->setUsuarioCreacion($user);
            $registroP->setFechaCreacion($ahora);
            $registroP->setDescripcion('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $registroP->setRef1($request->request->get('tipo_orden'));
            $registroP->setRef2($ordenProduccion->getId());
            $registroP->setTipo($proceso->getProceso());

            $em->persist($registroP);
            $em->flush();
          }

        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }
      if($request->request->get("notas") != ""){
        $notas = new ProcesoNotas();
        $notas->setProceso($proceso);
        $notas->setNotas($request->request->get("notas"));
        $notas->setUsuario($user);
        $em->persist($notas);
        $em->flush();
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Asignación de trabajo en proceso de CORTE');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('CORTE');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Asignación realizada correctamente'
      );

      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);

    }

    public function asignarProcesoDisenoAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      #var_dump($request->request);exit;

      $personas = $em->getRepository('AppBundle:EquipoTrabajo')
              ->createQueryBuilder('a')
              ->where('a.area = :area')
              ->setParameter('area', 'DISEÑO')
              ->getQuery()
              ->getResult();

      $params = $request->request->all();
      foreach ($params as $key => $param) {
        if(strpos($key, 'Q-') !== false){
          $index = explode("-",$key)[1];
          $persona_encargada = $this->getDoctrine()->getRepository(EquipoTrabajo::class)->find($request->request->get('P-'.$index));
          $encargado = new ProcesoEncargado();
          $encargado->setProceso($proceso);
          $encargado->setPersonaAsignada($persona_encargada);
          $encargado->setDiseno($diseno);
          $encargado->setCantidad($request->request->get('Q-'.$index));
          $encargado->setTalla($request->request->get('T-'.$index));
          $encargado->setEnProceso(true);
          $encargado->setFechaAsignacion($ahora);
          $em->persist($encargado);
          $em->flush();
        }
      }
      if($request->request->get("notas") != ""){
        $notas = new ProcesoNotas();
        $notas->setProceso($proceso);
        $notas->setNotas($request->request->get("notas"));
        $notas->setUsuario($user);
        $em->persist($notas);
        $em->flush();
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Asignación de trabajo en proceso de DISEÑO');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('DISEÑO');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Asignación realizada correctamente'
      );

      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function asignarProcesoBordadoAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      #var_dump($request->request);exit;

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosP = 0;
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if(strpos($key, 'Q-') !== false){
          $index = explode("-",$key)[1];
          $persona_encargada = $this->getDoctrine()->getRepository(EquipoTrabajo::class)->find($request->request->get('P-'.$index));
          $encargado = new ProcesoEncargado();
          $encargado->setProceso($proceso);
          $encargado->setPersonaAsignada($persona_encargada);
          $encargado->setDiseno($diseno);
          $encargado->setCantidad($request->request->get('Q-'.$index));
          $encargado->setTalla(0);
          $encargado->setMaterial($request->request->get('Pieza-'.$index));
          $encargado->setEnProceso(true);
          $encargado->setFechaAsignacion($ahora);
          $em->persist($encargado);
          $em->flush();
        }
        elseif (strpos($key, 'CC-') !== false) {
          $id_pieza = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $pieza = $em->getRepository('AppBundle:ProduccionBordado')
                  ->createQueryBuilder('a')
                  ->where('a.id = :id')
                  ->setParameter('id', $id_pieza)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosP += (int)$request->request->get($key);
          if($pieza->getCantidadConfirmada() > $request->request->get($key)){
            $pieza->setCantidadConfirmada($request->request->get($key));
            $cambiosQ = true;
            $novedadP = new ProduccionNovedad();
            $novedadP->setOrdenProduccion($ordenProduccion);
            $novedadP->setDiseno($disenoP);
            $novedadP->setNovedad('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor de '.$pieza->getTipo().' para bordar del diseño REF '.$diseno->getNombre());
            $novedadP->setTipo('Novedad en cantidades confirmadas');
            $novedadP->setFechaCreacion($ahora);
            $em->persist($novedadP);
            $em->flush();
          }

        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosP);
      }
      if($request->request->get("notas") != ""){
        $notas = new ProcesoNotas();
        $notas->setProceso($proceso);
        $notas->setNotas($request->request->get("notas"));
        $notas->setUsuario($user);
        $em->persist($notas);
        $em->flush();
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Asignación de trabajo en proceso de BORDADO');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('BORDADO');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Asignación realizada correctamente'
      );

      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show4', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show3', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function asignarProcesoConfeccionAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      #var_dump($request->request);exit;

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = 0;
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if(strpos($key, 'Q-') !== false){
          $index = explode("-",$key)[1];
          $persona_encargada = $this->getDoctrine()->getRepository(EquipoTrabajo::class)->find($request->request->get('P-'.$index));
          $encargado = new ProcesoEncargado();
          $encargado->setProceso($proceso);
          $encargado->setPersonaAsignada($persona_encargada);
          $encargado->setDiseno($diseno);
          $encargado->setCantidad($request->request->get('Q-'.$index));
          if($request->request->get('T-'.$index)==null){
            $encargado->setTalla("0");
            $encargado->setMaterial("PRETINAS");
          }
          else{
            $encargado->setTalla($request->request->get('T-'.$index));
          }
          $encargado->setEnProceso(true);
          $encargado->setFechaAsignacion($ahora);
          $em->persist($encargado);
          $em->flush();
        }
        elseif (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT += (int)$request->request->get($key);
          if($tallaP->getCantidadConfirmada() > $request->request->get($key)){
            $tallaP->setCantidadConfirmada($request->request->get($key));
            $cambiosQ = true;
            $novedadP = new ProduccionNovedad();
            $novedadP->setOrdenProduccion($ordenProduccion);
            $novedadP->setDiseno($disenoP);
            $novedadP->setNovedad('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $novedadP->setTipo('Cantidad de producción modificada');
            $novedadP->setFechaCreacion($ahora);
            $em->persist($novedadP);
            $em->flush();

            $registroP = new DisenoNovedad();
            $registroP->setDiseno($diseno);
            $registroP->setUsuarioCreacion($user);
            $registroP->setFechaCreacion($ahora);
            $registroP->setDescripcion('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $registroP->setRef1($request->request->get('tipo_orden'));
            $registroP->setRef2($ordenProduccion->getId());
            $registroP->setTipo($proceso->getProceso());

            $em->persist($registroP);
            $em->flush();
          }

        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }

      if($request->request->get("notas") != ""){
        $notas = new ProcesoNotas();
        $notas->setProceso($proceso);
        $notas->setNotas($request->request->get("notas"));
        $notas->setUsuario($user);
        $em->persist($notas);
        $em->flush();
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Asignación de trabajo en proceso de CONFECCIÓN');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('CONFECCION');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Asignación realizada correctamente'
      );

      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function asignarProcesoLavanderiaAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      #var_dump($request->request);exit;

      $personas = $em->getRepository('AppBundle:EquipoTrabajo')
              ->createQueryBuilder('a')
              ->where('a.area = :area')
              ->setParameter('area', 'LAVANDERIA')
              ->getQuery()
              ->getResult();

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = 0;
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if(strpos($key, 'Q-') !== false){
          $index = explode("-",$key)[1];
          $persona_encargada = $this->getDoctrine()->getRepository(EquipoTrabajo::class)->find($request->request->get('P-'.$index));
          $encargado = new ProcesoEncargado();
          $encargado->setProceso($proceso);
          $encargado->setPersonaAsignada($persona_encargada);
          $encargado->setDiseno($diseno);
          $encargado->setCantidad($request->request->get('Q-'.$index));
          $encargado->setTalla($request->request->get('T-'.$index));
          $encargado->setEnProceso(true);
          $encargado->setFechaAsignacion($ahora);
          $em->persist($encargado);
          $em->flush();
        }
        elseif (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT += (int)$request->request->get($key);
          if($tallaP->getCantidadConfirmada() > $request->request->get($key)){
            $tallaP->setCantidadConfirmada($request->request->get($key));
            $cambiosQ = true;
            $novedadP = new ProduccionNovedad();
            $novedadP->setOrdenProduccion($ordenProduccion);
            $novedadP->setDiseno($disenoP);
            $novedadP->setNovedad('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $novedadP->setTipo('Cantidad de producción modificada');
            $novedadP->setFechaCreacion($ahora);
            $em->persist($novedadP);
            $em->flush();

            $registroP = new DisenoNovedad();
            $registroP->setDiseno($diseno);
            $registroP->setUsuarioCreacion($user);
            $registroP->setFechaCreacion($ahora);
            $registroP->setDescripcion('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $registroP->setRef1($request->request->get('tipo_orden'));
            $registroP->setRef2($ordenProduccion->getId());
            $registroP->setTipo($proceso->getProceso());

            $em->persist($registroP);
            $em->flush();
          }

        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }

      if($request->request->get("notas") != ""){
        $notas = new ProcesoNotas();
        $notas->setProceso($proceso);
        $notas->setNotas($request->request->get("notas"));
        $notas->setUsuario($user);
        $em->persist($notas);
        $em->flush();
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Asignación de trabajo en proceso de LAVANDERIA');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('LAVANDERIA');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Asignación realizada correctamente'
      );

      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function asignarProcesoFotosAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      #var_dump($request->request);exit;

      $personas = $em->getRepository('AppBundle:EquipoTrabajo')
              ->createQueryBuilder('a')
              ->where('a.area = :area')
              ->setParameter('area', 'FOTOS')
              ->getQuery()
              ->getResult();

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = 0;
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if(strpos($key, 'Q-') !== false){
          $index = explode("-",$key)[1];
          $persona_encargada = $this->getDoctrine()->getRepository(EquipoTrabajo::class)->find($request->request->get('P-'.$index));
          $encargado = new ProcesoEncargado();
          $encargado->setProceso($proceso);
          $encargado->setPersonaAsignada($persona_encargada);
          $encargado->setDiseno($diseno);
          $encargado->setCantidad($request->request->get('Q-'.$index));
          $encargado->setTalla($request->request->get('T-'.$index));
          $encargado->setEnProceso(true);
          $encargado->setFechaAsignacion($ahora);
          $em->persist($encargado);
          $em->flush();
        }
        elseif (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT += (int)$request->request->get($key);
          if($tallaP->getCantidadConfirmada() > $request->request->get($key)){
            $tallaP->setCantidadConfirmada($request->request->get($key));
            $cambiosQ = true;
            $novedadP = new ProduccionNovedad();
            $novedadP->setOrdenProduccion($ordenProduccion);
            $novedadP->setDiseno($disenoP);
            $novedadP->setNovedad('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $novedadP->setTipo('Cantidad de producción modificada');
            $novedadP->setFechaCreacion($ahora);
            $em->persist($novedadP);
            $em->flush();

            $registroP = new DisenoNovedad();
            $registroP->setDiseno($diseno);
            $registroP->setUsuarioCreacion($user);
            $registroP->setFechaCreacion($ahora);
            $registroP->setDescripcion('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $registroP->setRef1($request->request->get('tipo_orden'));
            $registroP->setRef2($ordenProduccion->getId());
            $registroP->setTipo($proceso->getProceso());

            $em->persist($registroP);
            $em->flush();
          }

        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }

      if($request->request->get("notas") != ""){
        $notas = new ProcesoNotas();
        $notas->setProceso($proceso);
        $notas->setNotas($request->request->get("notas"));
        $notas->setUsuario($user);
        $em->persist($notas);
        $em->flush();
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Asignación de trabajo en proceso de LAVANDERIA');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('FOTOS');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Asignación realizada correctamente'
      );

      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function asignarProcesoPreterminadosAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      #var_dump($request->request);exit;

      $personas = $em->getRepository('AppBundle:EquipoTrabajo')
              ->createQueryBuilder('a')
              ->where('a.area = :area')
              ->setParameter('area', 'PRETERMINADOS')
              ->getQuery()
              ->getResult();

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = 0;
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if(strpos($key, 'Q-') !== false){
          $index = explode("-",$key)[1];
          $persona_encargada = $this->getDoctrine()->getRepository(EquipoTrabajo::class)->find($request->request->get('P-'.$index));
          $encargado = new ProcesoEncargado();
          $encargado->setProceso($proceso);
          $encargado->setPersonaAsignada($persona_encargada);
          $encargado->setDiseno($diseno);
          $encargado->setCantidad($request->request->get('Q-'.$index));
          if($request->request->get('tipo_orden') == "PRODUCCION"){
            $encargado->setTalla(0);
            $encargado->setMaterial($request->request->get('T-'.$index));
          }
          else{
            $encargado->setTalla($request->request->get('T-'.$index));
          }
          $encargado->setEnProceso(true);
          $encargado->setFechaAsignacion($ahora);
          $em->persist($encargado);
          $em->flush();
        }
        elseif (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT += (int)$request->request->get($key);
          if($tallaP->getCantidadConfirmada() > $request->request->get($key)){
            $tallaP->setCantidadConfirmada($request->request->get($key));
            $cambiosQ = true;
            $novedadP = new ProduccionNovedad();
            $novedadP->setOrdenProduccion($ordenProduccion);
            $novedadP->setDiseno($disenoP);
            $novedadP->setNovedad('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $novedadP->setTipo('Cantidad de producción modificada');
            $novedadP->setFechaCreacion($ahora);
            $em->persist($novedadP);
            $em->flush();

            $registroP = new DisenoNovedad();
            $registroP->setDiseno($diseno);
            $registroP->setUsuarioCreacion($user);
            $registroP->setFechaCreacion($ahora);
            $registroP->setDescripcion('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $registroP->setRef1($request->request->get('tipo_orden'));
            $registroP->setRef2($ordenProduccion->getId());
            $registroP->setTipo($proceso->getProceso());

            $em->persist($registroP);
            $em->flush();
          }

        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }

      if($request->request->get("notas") != ""){
        $notas = new ProcesoNotas();
        $notas->setProceso($proceso);
        $notas->setNotas($request->request->get("notas"));
        $notas->setUsuario($user);
        $em->persist($notas);
        $em->flush();
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Asignación de trabajo en proceso de PRETERMINADOS');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('PRETERMINADOS');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Asignación realizada correctamente'
      );

      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function asignarProcesoTerminadosAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      #var_dump($request->request);exit;

      $personas = $em->getRepository('AppBundle:EquipoTrabajo')
              ->createQueryBuilder('a')
              ->where('a.area = :area')
              ->setParameter('area', 'TERMINADOS')
              ->getQuery()
              ->getResult();

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = 0;
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if(strpos($key, 'Q-') !== false){
          $index = explode("-",$key)[1];
          $persona_encargada = $this->getDoctrine()->getRepository(EquipoTrabajo::class)->find($request->request->get('P-'.$index));
          $encargado = new ProcesoEncargado();
          $encargado->setProceso($proceso);
          $encargado->setPersonaAsignada($persona_encargada);
          $encargado->setDiseno($diseno);
          $encargado->setCantidad($request->request->get('Q-'.$index));
          if($request->request->get('tipo_orden') == "PRODUCCION"){
            $encargado->setTalla(0);
            $encargado->setMaterial($request->request->get('MT-'.$index));
          }
          else{
            $encargado->setTalla($request->request->get('T-'.$index));
          }
          $encargado->setCantidadMaterial($request->request->get('MQ0-'.$index));
          $encargado->setEnProceso(true);
          $encargado->setFechaAsignacion($ahora);
          $em->persist($encargado);
          $em->flush();
        }
        elseif (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT += (int)$request->request->get($key);
          if($tallaP->getCantidadConfirmada() > $request->request->get($key)){
            $tallaP->setCantidadConfirmada($request->request->get($key));
            $cambiosQ = true;
            $novedadP = new ProduccionNovedad();
            $novedadP->setOrdenProduccion($ordenProduccion);
            $novedadP->setDiseno($disenoP);
            $novedadP->setNovedad('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $novedadP->setTipo('Cantidad de producción modificada');
            $novedadP->setFechaCreacion($ahora);
            $em->persist($novedadP);
            $em->flush();

            $registroP = new DisenoNovedad();
            $registroP->setDiseno($diseno);
            $registroP->setUsuarioCreacion($user);
            $registroP->setFechaCreacion($ahora);
            $registroP->setDescripcion('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño REF '.$diseno->getNombre());
            $registroP->setRef1($request->request->get('tipo_orden'));
            $registroP->setRef2($ordenProduccion->getId());
            $registroP->setTipo($proceso->getProceso());

            $em->persist($registroP);
            $em->flush();
          }

        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }
      if($request->request->get("notas") != ""){
        $notas = new ProcesoNotas();
        $notas->setProceso($proceso);
        $notas->setNotas($request->request->get("notas"));
        $notas->setUsuario($user);
        $em->persist($notas);
        $em->flush();
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Asignación de trabajo en proceso de TERMINADOS');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('TERMINADOS');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Asignación realizada correctamente'
      );

      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function asignarProcesoEmpaqueAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      #var_dump($request->request);exit;

      $personas = $em->getRepository('AppBundle:EquipoTrabajo')
              ->createQueryBuilder('a')
              ->where('a.area = :area')
              ->setParameter('area', 'EMPAQUE')
              ->getQuery()
              ->getResult();

      $params = $request->request->all();
      $cambiosQ = false;
      $cambiosT = 0;
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
      }
      foreach ($params as $key => $param) {
        if(strpos($key, 'Q-') !== false){
          $index = explode("-",$key)[1];
          $persona_encargada = $this->getDoctrine()->getRepository(EquipoTrabajo::class)->find($request->request->get('P-'.$index));
          $encargado = new ProcesoEncargado();
          $encargado->setProceso($proceso);
          $encargado->setPersonaAsignada($persona_encargada);
          $encargado->setDiseno($diseno);
          $encargado->setCantidad($request->request->get('Q-'.$index));
          $encargado->setTalla(0);
          $encargado->setEnProceso(true);
          $encargado->setFechaAsignacion($ahora);
          $em->persist($encargado);
          $em->flush();
        }
        elseif (strpos($key, 'CC-') !== false) {
          $talla = explode("-",$key)[1];
          $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                  ->createQueryBuilder('a')
                  ->where('a.diseno = :design')
                  ->setParameter('design', $diseno->getId())
                  ->andWhere('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $tallaP = $em->getRepository('AppBundle:ProduccionTalla')
                  ->createQueryBuilder('a')
                  ->where('a.ordenProduccion = :order')
                  ->setParameter('order', $ordenProduccion)
                  ->andwhere('a.diseno = :design')
                  ->setParameter('design', $disenoP)
                  ->andwhere('a.talla = :Talla')
                  ->setParameter('Talla', $talla)
                  ->setMaxResults(1)
                  ->getQuery()
                  ->getOneOrNullResult();
          $cambiosT += (int)$request->request->get($key);
          if($tallaP->getCantidadConfirmada() > $request->request->get($key)){
            $tallaP->setCantidadConfirmada($request->request->get($key));
            $cambiosQ = true;
            $novedadP = new ProduccionNovedad();
            $novedadP->setOrdenProduccion($ordenProduccion);
            $novedadP->setDiseno($disenoP);
            $novedadP->setNovedad('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño '.$diseno->getNombre());
            $novedadP->setTipo('Cantidad de producción modificada');
            $novedadP->setFechaCreacion($ahora);
            $em->persist($novedadP);
            $em->flush();

            $registroP = new DisenoNovedad();
            $registroP->setDiseno($diseno);
            $registroP->setUsuarioCreacion($user);
            $registroP->setFechaCreacion($ahora);
            $registroP->setDescripcion('El supervisor de '.$proceso->getProceso().' confirma recibir una cantidad menor para la talla '.$talla.' del diseño '.$diseno->getNombre());
            $registroP->setRef1($request->request->get('tipo_orden'));
            $registroP->setRef2($ordenProduccion->getId());
            $registroP->setTipo($proceso->getProceso());

            $em->persist($registroP);
            $em->flush();
          }

        }
      }
      if($cambiosQ){
        $proceso->setCantidad($cambiosT);
      }
      if($request->request->get("notas") != ""){
        $notas = new ProcesoNotas();
        $notas->setProceso($proceso);
        $notas->setNotas($request->request->get("notas"));
        $notas->setUsuario($user);
        $em->persist($notas);
        $em->flush();
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Asignación de trabajo en proceso de EMPAQUE');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('EMPAQUE');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Asignación realizada correctamente'
      );

      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);
    }

    public function terminarProcesosAction(Request $request, Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $proceso->getFechaInicio();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );
      $orden = $diseno->getOrden();
      $inicio2 = $orden->getFechaCreacion();
      $diff2 = $inicio2->diff($ahora);
      $duracion2 = ($diff2->d * 1440) + ($diff2->h * 60) + ( $diff2->i );
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
        $disenoP = $em->getRepository('AppBundle:ProduccionDiseno')
                ->createQueryBuilder('a')
                ->where('a.diseno = :design')
                ->setParameter('design', $diseno->getId())
                ->andWhere('a.ordenProduccion = :order')
                ->setParameter('order', $ordenProduccion)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        $inicioD = $disenoP->getFechaCreacion();
        $diffD = $inicioD->diff($ahora);
        $duracionD = ($diffD->d * 1440) + ($diffD->h * 60) + ( $diffD->i );
        $disenoP->setEstado(2);
        $disenoP->setFechaFinalizacion($ahora);
        $disenoP->setDuracion($duracionD);
      }
      $proceso->setFechaFinalizacion($ahora);
      $proceso->setStatus(2);
      $proceso->setDuracion($duracion);
      $checked = true;
      if($request->request->get('tipo_orden') == "DISEÑO"){
        $check_disenos_procesos = $em->getRepository('AppBundle:Proceso')
                ->createQueryBuilder('a')
                ->where('a.tipoOrden = :type')
                ->setParameter('type', $request->request->get('tipo_orden'))
                ->andwhere('a.orden = :order')
                ->setParameter('order', $diseno->getOrden()->getId())
                ->getQuery()
                ->getResult();

        foreach ($check_disenos_procesos as $item) {
          if($item->getStatus() == 1){
            $checked = false;
          }
        }
        if($checked){
          $orden->setFechaFinalizacion($ahora);
          $orden->setDuracion($duracion2);
          $orden->setEstado(2);
        }
      }
      else{
        $check_disenos_procesos = $em->getRepository('AppBundle:ProduccionDiseno')
                ->createQueryBuilder('a')
                ->where('a.ordenProduccion = :order')
                ->setParameter('order',$ordenProduccion)
                ->getQuery()
                ->getResult();

        foreach ($check_disenos_procesos as $item) {
          if($item->getEstado() == 1){
            $checked = false;
          }
        }
        if($checked){
          $ordenProduccionP = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->request->get('orden_produccion'));
          $inicioP = $ordenProduccionP->getFechaCreacion();
          $diffP = $inicioP->diff($ahora);
          $duracionP = ($diffP->d * 1440) + ($diffP->h * 60) + ( $diffP->i );
          $ordenProduccionP->setFechaFinalizacion($ahora);
          $ordenProduccionP->setEstado(2);
          $ordenProduccionP->setDuracion($duracionP);
        }
      }
      $params = $request->request->all();
      foreach ($params as $key => $param) {
        if(strpos($key, 'Q-') !== false){
          $index = explode("-",$key)[1];
          $caja = new ProduccionEmpaque();
          $caja->setOrdenProduccion($ordenProduccion);
          $caja->setDiseno($disenoP);
          $caja->setCaja($index);
          $caja->setCantidad($request->request->get($key));
          $caja->setMarca($request->request->get('marca-'.$index));
          $caja->setCurva($request->request->get('curva-'.$index));
          $caja->setNotas($request->request->get('nota-'.$index));
          $caja->setFechaCreacion($ahora);
          $em->persist($caja);
          $em->flush();
        }
      }
      if($request->request->get("notas") != ""){
        $novedadP = new ProduccionNovedad();
        $novedadP->setOrdenProduccion($ordenProduccion);
        $novedadP->setDiseno($disenoP);
        $novedadP->setNovedad($request->request->get("notas"));
        $novedadP->setTipo('Aclaraciones de Empaque');
        $novedadP->setFechaCreacion($ahora);
        $em->persist($novedadP);
        $em->flush();
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Proceso de '.$proceso->getProceso().' Terminado');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo($proceso->getProceso());

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Proceso terminado correctamente'
      );

      if($request->request->get('tipo_orden') == "DISEÑO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$ordenProduccion->getId()]);

    }

    public function terminarProcesoBordadoAction(Diseno $diseno, Proceso $proceso){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $proceso->getFechaInicio();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );

      $proceso->setFechaFinalizacion($ahora);
      $proceso->setStatus(2);
      $proceso->setDuracion($duracion);

      $asignacion = $em->getRepository('AppBundle:ProcesoEncargado')
              ->createQueryBuilder('a')
              ->where('a.proceso = :process')
              ->setParameter('process', $proceso->getId())
              ->getQuery()
              ->getResult();

      foreach ($asignacion as $encargo) {
        $object = $this->getDoctrine()->getRepository(ProcesoEncargado::class)->find($encargo->getId());
        $object->setEnProceso(false);
      }

      $registro = new DisenoNovedad();
      $registro->setDiseno($diseno);
      $registro->setUsuarioCreacion($user);
      $registro->setFechaCreacion($ahora);
      $registro->setDescripcion('Proceso de Bordado Terminado');
      $registro->setRef1($request->request->get('tipo_orden'));
      if($request->request->get('tipo_orden') == "PRODUCCION"){
        $registro->setRef2($ordenProduccion->getId());
      }
      $registro->setTipo('BORDADO');

      $em->persist($registro);
      $em->flush();

      $this->addFlash(
        'success',
        'Proceso terminado correctamente'
      );

      return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
    }


}
