<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\DisenoOrden;


class OrdenesController extends Controller
{

    /**
     */
    public function indexAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $disenos = $em->getRepository('AppBundle:Diseno')
              ->createQueryBuilder('a')
              ->getQuery()
              ->getResult();
      $ordenes = $em->getRepository('AppBundle:DisenoOrden')
              ->createQueryBuilder('c')
              ->getQuery()
              ->getResult();
      $procesos_activos = $em->getRepository('AppBundle:Proceso')
              ->createQueryBuilder('a')
              ->where('a.status = 1')
              ->orWhere('a.status = 0')
              ->getQuery()
              ->getResult();
      return $this->render('home/index.html.twig', array(
        'ordenes' => $ordenes,
        'procesos_activos' => $procesos_activos,
        'disenos' => $disenos
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

      return $this->render('produccion/index.html.twig', array(
      ));
    }

}
