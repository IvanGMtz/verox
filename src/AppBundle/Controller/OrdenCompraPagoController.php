<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenCompraPago;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

use AppBundle\Entity\OrdenCompra;
use AppBundle\Entity\OrdenCompraNovedad;


/**
 * Ordencomprapago controller.
 *
 */
class OrdenCompraPagoController extends Controller
{
    /**
     * Lists all ordenCompraPago entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator, OrdenCompra $ordenCompra)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.OrdenCompraPago'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $ordenCompraPagosQ = $em->getRepository('AppBundle:OrdenCompraPago')->createQueryBuilder('a')
                ->where('a.ordenCompra = :ordenCompra')
                ->setParameter('ordenCompra', $ordenCompra);
        
        if($q && $q !=''){
          $this->get('session')->set('q.OrdenCompraPago', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $ordenCompraPagosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $ordenCompraPagosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $ordenCompraPagosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $ordenCompraPagos = $pagination->getItems();
        
        $ordenCompraPago = new Ordencomprapago();
        $form = $this->createForm('AppBundle\Form\OrdenCompraPagoType', $ordenCompraPago);
        

        return $this->render('ordencomprapago/index.html.twig', array(
            'ordenCompraPagos' => $ordenCompraPagos,
            'q' => $q,
            'pagination' => $pagination,
            'ordenCompra' => $ordenCompra,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new ordenCompraPago entity.
     *
     */
    public function newAction(Request $request, OrdenCompra $ordenCompra)
    {
        $ordenCompraPago = new Ordencomprapago();
        $ordenCompraPago->setOrdenCompra($ordenCompra);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();
        $ordenCompraPago->setFechaCreacion($fecha);
        $ordenCompraPago->setUsuarioCreacion($user);
        $ordenCompraPago->setEstado(1);
        
        $form = $this->createForm('AppBundle\Form\OrdenCompraPagoType', $ordenCompraPago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
            if($ordenCompra->getValorSaldo() <= 0 && $ordenCompra->getPagada()){
              $this->addFlash(
                'error',
                'La orden de compra ya ha sido pagado enteramente'
              );
              return $this->redirectToRoute('ordencompra_show', ['id'=> $ordenCompra->getId()]);
            }
            
            if($ordenCompraPago->getValor() > $ordenCompra->getValorSaldo()){
              $this->addFlash(
                'error',
                'El pago excede el saldo de la orden'
              );
              return $this->redirectToRoute('ordencompra_show', ['id'=> $ordenCompra->getId()]);
            }
            
            $em = $this->getDoctrine()->getManager();
            
            $ordenCompraPago->setOrdenCompra($ordenCompra);
            $ordenCompraPago->setFechaCreacion($fecha);
            $ordenCompraPago->setUsuarioCreacion($user);
            
            $em->persist($ordenCompraPago);
            $em->flush($ordenCompraPago);
            
            $pagos = $em->getRepository('AppBundle:OrdenCompraPago')
                    ->createQueryBuilder('a')
                    ->where('a.ordenCompra = :ordenCompra')
                    ->setParameter('ordenCompra', $ordenCompra)
                    ->getQuery()
                    ->getResult()
                    ;
            $valorPagado = 0;
            foreach($pagos as $pago){
              if($pago->getEstado() == 1){
                $valorPagado += $pago->getValor();
              }
            }
            $ordenCompra->setValorPagado($valorPagado);
            $ordenCompra->setValorSaldo($ordenCompra->getValorTotal()-$valorPagado);
            if($ordenCompra->getValorSaldo() < 0){$ordenCompra->setValorSaldo(0);}
            
            if($ordenCompra->getValorSaldo() <= 0){
              $ordenCompra->setPagada(true);
              $ordenCompra->setFechaPagada($fecha);
              $this->addFlash(
                  'success',
                  'Registro creado correctamente, orden pagada en su totalidad'
              );
            }else{
              $this->addFlash(
                  'success',
                  'Registro creado correctamente, saldo: '.number_format($ordenCompra->getValorSaldo(), 2, ',', '.')
              );
            }
            
            $em->persist($ordenCompra);
            $em->flush($ordenCompra);
            
            $novedad = new OrdenCompraNovedad();
            $novedad->setFechaCreacion($fecha);
            $novedad->setTipo('PAGO GENERADO');
            $novedad->setUsuarioCreacion($user);
            $novedad->setOrdenCompra($ordenCompra);
            $novedad->setDescripcion('Pago generado: '.number_format($ordenCompraPago->getValor(), 2, ',', '.').', saldo: '.number_format($ordenCompra->getValorSaldo(), 2, ',', '.'));
            $novedad->setRef1($ordenCompraPago->getId());
            $novedad->setRef2($ordenCompraPago->getValor());
            $em->persist($novedad);
            $em->flush($novedad);
            
            return $this->redirectToRoute('ordencompra_show', ['id'=> $ordenCompra->getId()]);

        }

        return $this->redirectToRoute('ordencompra_show', ['id'=> $ordenCompra->getId()]);
    }
    
    public function anularAction(OrdenCompraPago $ordenCompraPago)
    {
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $fecha = new \DateTime();
      $ordenCompra = $ordenCompraPago->getOrdenCompra();
      
      if($ordenCompraPago->getEstado() != 1){
        $this->addFlash('error', 'Pago ya ha sido anulado');
        return $this->redirectToRoute('ordencompra_show',['id'=>$ordenCompra->getId()]);
      }
      
      if($ordenCompraPago->getOrdenCompra()->getEstado() == 3){
        $this->addFlash('error', 'Orden cerrada');
        return $this->redirectToRoute('ordencompra_show',['id'=>$ordenCompra->getId()]);
      }
      
      $ordenCompraPago->setEstado(0);
      $em->persist($ordenCompraPago);
      $em->flush($ordenCompraPago);
      
      $pagos = $em->getRepository('AppBundle:OrdenCompraPago')
              ->createQueryBuilder('a')
              ->where('a.ordenCompra = :ordenCompra')
              ->setParameter('ordenCompra', $ordenCompra)
              ->getQuery()
              ->getResult()
              ;
      $valorPagado = 0;
      foreach($pagos as $pago){
        if($pago->getEstado() == 1){
          $valorPagado += $pago->getValor();
        }
      }
      $ordenCompra->setValorPagado($valorPagado);
      $ordenCompra->setValorSaldo($ordenCompra->getValorTotal()-$valorPagado);
      if($ordenCompra->getValorSaldo() < 0){$ordenCompra->setValorSaldo(0);}
      
      if($ordenCompra->getValorSaldo() <= 0){
        $ordenCompra->setPagada(true);
        $ordenCompra->setFechaPagada($fecha);
      }else{
        $ordenCompra->setPagada(false);
        $ordenCompra->setFechaPagada(null);
      }
      
      $em->persist($ordenCompra);
      $em->flush($ordenCompra);
      
      $novedad = new OrdenCompraNovedad();
      $novedad->setFechaCreacion($fecha);
      $novedad->setTipo('PAGO ANULADO');
      $novedad->setUsuarioCreacion($user);
      $novedad->setOrdenCompra($ordenCompra);
      $novedad->setDescripcion('Pago anulado: '.number_format($ordenCompraPago->getValor(), 2, ',', '.'));
      $novedad->setRef1($ordenCompraPago->getId());
      $novedad->setRef2($ordenCompraPago->getValor());
      $em->persist($novedad);
      $em->flush($novedad);
      
      $this->addFlash('success', 'Pago anulado');
      return $this->redirectToRoute('ordencompra_show',['id'=>$ordenCompra->getId()]);
    }

    /**
     * Finds and displays a ordenCompraPago entity.
     *
     */
//    public function showAction(OrdenCompraPago $ordenCompraPago)
//    {
//        $deleteForm = $this->createDeleteForm($ordenCompraPago);
//
//        return $this->render('ordencomprapago/show.html.twig', array(
//            'ordenCompraPago' => $ordenCompraPago,
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

    /**
     * Displays a form to edit an existing ordenCompraPago entity.
     *
     */
//    public function editAction(Request $request, OrdenCompraPago $ordenCompraPago)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $deleteForm = $this->createDeleteForm($ordenCompraPago);
//        $editForm = $this->createForm('AppBundle\Form\OrdenCompraPagoType', $ordenCompraPago);
//        $editForm->handleRequest($request);
//        
//        $user = $this->container->get('security.token_storage')->getToken()->getUser();
//        $fecha = new \DateTime();
//
//        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $em->flush();
//            $this->addFlash(
//                'success',
//                'Registro editado correctamente'
//            );
//            return $this->redirectToRoute('ordencomprapago_index');
//        }
//
//        return $this->render('ordencomprapago/edit.html.twig', array(
//            'ordenCompraPago' => $ordenCompraPago,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

    /**
     * Deletes a ordenCompraPago entity.
     *
     */
//    public function deleteAction(Request $request, OrdenCompraPago $ordenCompraPago)
//    {
//        $form = $this->createDeleteForm($ordenCompraPago);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($ordenCompraPago);
//            $em->flush($ordenCompraPago);
//        }
//
//        return $this->redirectToRoute('ordencomprapago_index');
//    }

    /**
     * Creates a form to delete a ordenCompraPago entity.
     *
     * @param OrdenCompraPago $ordenCompraPago The ordenCompraPago entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createDeleteForm(OrdenCompraPago $ordenCompraPago)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('ordencomprapago_delete', array('id' => $ordenCompraPago->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }
//    
//    public function eraseAction(OrdenCompraPago $ordenCompraPago)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $em->remove($ordenCompraPago);
//        $em->flush($ordenCompraPago);
//        $this->addFlash(
//            'success',
//            'Registro eliminado correctamente'
//        );
//        return $this->redirectToRoute('ordencomprapago_index');
//    }
}
