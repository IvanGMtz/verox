<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenCompraNovedad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

use AppBundle\Entity\OrdenCompra;


/**
 * Ordencompranovedad controller.
 *
 */
class OrdenCompraNovedadController extends Controller
{
    /**
     * Lists all ordenCompraNovedad entities.
     *
     */
    public function indexAction(OrdenCompra $ordenCompra)
    {
        $em = $this->getDoctrine()->getManager();
        $ordenCompraNovedads = $em->getRepository('AppBundle:OrdenCompraNovedad')->createQueryBuilder('a')
                ->where('a.ordenCompra = :ordenCompra')->setParameter('ordenCompra', $ordenCompra)
                ->orderBy('a.fechaCreacion', 'DESC')->addOrderBy('a.id', 'DESC')->getQuery()->getResult();
        return $this->render('ordencompranovedad/index.html.twig', array(
            'ordenCompraNovedads' => $ordenCompraNovedads,
            'ordenCompra' => $ordenCompra
        ));
    }

    /**
     * Creates a new ordenCompraNovedad entity.
     *
     */
    public function newAction(Request $request)
    {
        $ordenCompraNovedad = new Ordencompranovedad();
        $form = $this->createForm('AppBundle\Form\OrdenCompraNovedadType', $ordenCompraNovedad);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ordenCompraNovedad);
            $em->flush($ordenCompraNovedad);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('ordencompranovedad_index');

        }

        return $this->render('ordencompranovedad/new.html.twig', array(
            'ordenCompraNovedad' => $ordenCompraNovedad,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ordenCompraNovedad entity.
     *
     */
    public function showAction(OrdenCompraNovedad $ordenCompraNovedad)
    {
        $deleteForm = $this->createDeleteForm($ordenCompraNovedad);

        return $this->render('ordencompranovedad/show.html.twig', array(
            'ordenCompraNovedad' => $ordenCompraNovedad,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ordenCompraNovedad entity.
     *
     */
    public function editAction(Request $request, OrdenCompraNovedad $ordenCompraNovedad)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($ordenCompraNovedad);
        $editForm = $this->createForm('AppBundle\Form\OrdenCompraNovedadType', $ordenCompraNovedad);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('ordencompranovedad_index');
        }

        return $this->render('ordencompranovedad/edit.html.twig', array(
            'ordenCompraNovedad' => $ordenCompraNovedad,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    public function resolverModalAction(OrdenCompraNovedad $ordenCompraNovedad)
    {
      return $this->render('ordencompranovedad/modales/resolver.html.twig', array(
          'ordenCompraNovedad' => $ordenCompraNovedad,
      ));
    }
    
    public function resolverAction(Request $request, OrdenCompraNovedad $ordenCompraNovedad)
    {
        $em = $this->getDoctrine()->getManager();
        $anotaciones = $request->request->get('anotaciones', false);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();
        
        if(!$ordenCompraNovedad->getTienePendientes()){
          $this->addFlash(
              'error',
              'El item ya ha sido procesado'
          );
          return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompraNovedad->getOrdenCompra()->getId()]);
        }
        
        $ordenCompraNovedad->setTienePendientes(false);
        $em->persist($ordenCompraNovedad);
        
        $novedad = new OrdenCompraNovedad();
        $novedad->setFechaCreacion($fecha);
        $novedad->setTipo('RESOLUCION NOVEDAD');
        $novedad->setUsuarioCreacion($user);
        $novedad->setOrdenCompra($ordenCompraNovedad->getOrdenCompra());
        $novedad->setDescripcion('Novedad #'.$ordenCompraNovedad->getId().' resuelta: '.$anotaciones);
        $novedad->setAnotaciones($anotaciones);
        $novedad->setTienePendientes(false);
        $em->persist($novedad);
        
        $em->flush();
        
        $ordenCompra = $ordenCompraNovedad->getOrdenCompra();
        $checkItemsAbiertos = $em->getRepository('AppBundle:OrdenCompraNovedad')
                ->createQueryBuilder('a')
                ->where('a.ordenCompra = :ordenCompra')
                ->andWhere('a.tienePendientes = 1')
                ->setParameter('ordenCompra', $ordenCompra)
                ->getQuery()
                ->getResult()
                ;
        if(count($checkItemsAbiertos) == 0){
          $ordenCompra->setTienePendientes(false);
          $em->persist($ordenCompra);
          $em->flush();
        }
        
        $this->addFlash(
            'success',
            'Novedad solucionada correctamente'
        );
        return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompra->getId()]);
    }

    /**
     * Deletes a ordenCompraNovedad entity.
     *
     */
//    public function deleteAction(Request $request, OrdenCompraNovedad $ordenCompraNovedad)
//    {
//        $form = $this->createDeleteForm($ordenCompraNovedad);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($ordenCompraNovedad);
//            $em->flush($ordenCompraNovedad);
//        }
//
//        return $this->redirectToRoute('ordencompranovedad_index');
//    }

    /**
     * Creates a form to delete a ordenCompraNovedad entity.
     *
     * @param OrdenCompraNovedad $ordenCompraNovedad The ordenCompraNovedad entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createDeleteForm(OrdenCompraNovedad $ordenCompraNovedad)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('ordencompranovedad_delete', array('id' => $ordenCompraNovedad->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }
//    
//    public function eraseAction(OrdenCompraNovedad $ordenCompraNovedad)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $em->remove($ordenCompraNovedad);
//        $em->flush($ordenCompraNovedad);
//        $this->addFlash(
//            'success',
//            'Registro eliminado correctamente'
//        );
//        return $this->redirectToRoute('ordencompranovedad_index');
//    }
}
