<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AlmacenZonaInventario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

use AppBundle\Entity\OrdenCompraItem;
use AppBundle\Entity\InventarioMovimiento;
use AppBundle\Entity\Inventario;
use AppBundle\Entity\OrdenCompraNovedad;


/**
 * Almacenzonainventario controller.
 *
 */
class AlmacenZonaInventarioController extends Controller
{
    /**
     * Lists all almacenZonaInventario entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.AlmacenZonaInventario'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }

        $almacenZonaInventariosQ = $em->getRepository('AppBundle:AlmacenZonaInventario')
        ->createQueryBuilder('a')
        ->join('a.material','m')
        ->join('a.almacenZona','z');

        if($q && $q !=''){
          $this->get('session')->set('q.AlmacenZonaInventario', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($field == "id"){
                if($qcount == 0){
                  $almacenZonaInventariosQ->where('m.nombre LIKE :material')->setParameter('material', '%'.$value.'%');
                }else{
                  $almacenZonaInventariosQ->andWhere('m.nombre LIKE :material')->setParameter('material', '%'.$value.'%');
                }
                $qcount++;
              }
              else if($field == "ingresoTotal"){
                if($qcount == 0){
                  $almacenZonaInventariosQ->where('z.ubicacion LIKE :ubicacion')->setParameter('ubicacion', $value);
                }else{
                  $almacenZonaInventariosQ->andWhere('z.ubicacion LIKE :ubicacion')->setParameter('ubicacion', $value);
                }
                $qcount++;
              }
            }
          }
        }

        $query = $almacenZonaInventariosQ->orderBy('a.id','ASC')->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );

        $almacenZonaInventarios = $pagination->getItems();


        return $this->render('almacenzonainventario/index.html.twig', array(
            'almacenZonaInventarios' => $almacenZonaInventarios,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new almacenZonaInventario entity.
     *
     */
    public function newAction(Request $request, OrdenCompraItem $ordenCompraItem)
    {
        $em = $this->getDoctrine()->getManager();
        $almacenZonaInventario = new AlmacenZonaInventario();
        $almacenZonaInventario->setCantidadActual($ordenCompraItem->getCantidad());
        $form = $this->createForm('AppBundle\Form\AlmacenZonaInventarioType', $almacenZonaInventario, ['blocked' => true]);
        $form->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if($ordenCompraItem->getEnInventario() || $ordenCompraItem->getOrdenCompra()->getEstado() != 2){
          $this->addFlash(
              'error',
              'El item ya ha sido cargado a inventario'
          );
          return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompraItem->getOrdenCompra()->getId()]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
          $inventarioService = $this->container->get('app.inventario');
          $inventarioService->agregarInventario($ordenCompraItem, $almacenZonaInventario, $user);
          $this->addFlash(
              'success',
              'Item ingresado correctamente a inventario'
          );
          return $this->redirectToRoute('ordencompra_show', ['id' => $ordenCompraItem->getOrdenCompra()->getId()]);
        }

        return $this->render('almacenzonainventario/new.html.twig', array(
            'almacenZonaInventario' => $almacenZonaInventario,
            'form' => $form->createView(),
            'ordenCompraItem' => $ordenCompraItem
        ));
    }

    /**
     * Finds and displays a almacenZonaInventario entity.
     *
     */
    public function showAction(AlmacenZonaInventario $almacenZonaInventario)
    {
        #$deleteForm = $this->createDeleteForm($almacenZonaInventario);

        return $this->render('almacenzonainventario/show.html.twig', array(
            'almacenZonaInventario' => $almacenZonaInventario,
            #'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing almacenZonaInventario entity.
     *
     */
    public function editAction(Request $request, AlmacenZonaInventario $almacenZonaInventario)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($almacenZonaInventario);
        $editForm = $this->createForm('AppBundle\Form\AlmacenZonaInventarioType', $almacenZonaInventario);
        $editForm->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('almacenzonainventario_index');
        }

        return $this->render('almacenzonainventario/edit.html.twig', array(
            'almacenZonaInventario' => $almacenZonaInventario,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a almacenZonaInventario entity.
     *
     */
//    public function deleteAction(Request $request, AlmacenZonaInventario $almacenZonaInventario)
//    {
//        $form = $this->createDeleteForm($almacenZonaInventario);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($almacenZonaInventario);
//            $em->flush($almacenZonaInventario);
//        }
//
//        return $this->redirectToRoute('almacenzonainventario_index');
//    }

    /**
     * Creates a form to delete a almacenZonaInventario entity.
     *
     * @param AlmacenZonaInventario $almacenZonaInventario The almacenZonaInventario entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createDeleteForm(AlmacenZonaInventario $almacenZonaInventario)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('almacenzonainventario_delete', array('id' => $almacenZonaInventario->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }
//
//    public function eraseAction(AlmacenZonaInventario $almacenZonaInventario)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $em->remove($almacenZonaInventario);
//        $em->flush($almacenZonaInventario);
//        $this->addFlash(
//            'success',
//            'Registro eliminado correctamente'
//        );
//        return $this->redirectToRoute('almacenzonainventario_index');
//    }
}
