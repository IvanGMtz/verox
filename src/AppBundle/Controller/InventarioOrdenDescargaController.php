<?php

namespace AppBundle\Controller;

use AppBundle\Entity\InventarioOrdenDescarga;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

use AppBundle\Entity\InventarioOrdenItem;


/**
 * Inventarioordendescarga controller.
 *
 */
class InventarioOrdenDescargaController extends Controller
{
    /**
     * Lists all inventarioOrdenDescarga entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.InventarioOrdenDescarga'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }

        $inventarioOrdenDescargasQ = $em->getRepository('AppBundle:InventarioOrdenDescarga')->createQueryBuilder('a');

        if($q && $q !=''){
          $this->get('session')->set('q.InventarioOrdenDescarga', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $inventarioOrdenDescargasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $inventarioOrdenDescargasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }

        $query = $inventarioOrdenDescargasQ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );

        $inventarioOrdenDescargas = $pagination->getItems();


        return $this->render('inventarioordendescarga/index.html.twig', array(
            'inventarioOrdenDescargas' => $inventarioOrdenDescargas,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new inventarioOrdenDescarga entity.
     *
     */
    public function newAction(Request $request, InventarioOrdenItem $inventarioOrdenItem)
    {
        $em = $this->getDoctrine()->getManager();
        $inventarioOrdenDescarga = new Inventarioordendescarga();
        $inventarioOrdenDescarga->setCantidad($inventarioOrdenItem->getCantidad());

        $form = $this->createForm('AppBundle\Form\InventarioOrdenDescargaType', $inventarioOrdenDescarga, ['material' => $inventarioOrdenItem->getMaterial(), 'blocked' => true]);
        $form->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if($inventarioOrdenItem->getEntregado() || $inventarioOrdenItem->getInventarioOrden()->getEstado() != 2){
          $this->addFlash(
              'error',
              'El item ya ha sido descargado del inventario'
          );
          return $this->redirectToRoute('inventarioorden_show', ['id' => $inventarioOrdenItem->getInventarioOrden()->getId()]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // $inventarioOrdenDescarga->setInventarioOrden($inventarioOrdenItem->getInventarioOrden());
            // $inventarioOrdenDescarga->setInventarioOrdenItem($inventarioOrdenItem);
            // $inventarioOrdenDescarga->setMaterial($inventarioOrdenItem->getMaterial());
            // $inventarioOrdenDescarga->setAlmacen($inventarioOrdenDescarga->getAlmacenZonaInventario()->getAlmacen());
            // $inventarioOrdenDescarga->setAlmacenZona($inventarioOrdenDescarga->getAlmacenZonaInventario()->getZona());
            // $inventarioOrdenDescarga->setCantidad($request->request->get('appbundle_inventarioordendescarga')['cantidad']);
            // $inventarioOrdenDescarga->setValorUnitario($inventarioOrdenItem->getMaterial()->getCostoActual()?$inventarioOrdenItem->getMaterial()->getCostoActual():0);
            // $inventarioOrdenDescarga->setValorTotal($inventarioOrdenDescarga->getValorUnitario() * $inventarioOrdenItem->getCantidad());
            // $inventarioOrdenDescarga->setFechaCreacion($fecha);
            // $inventarioOrdenDescarga->setUsuarioCreacion($user);
            // $inventarioOrdenDescarga->setEstado(1);


            $inventarioService = $this->container->get('app.inventario');
            $resp = $inventarioService->descargarInventario($inventarioOrdenItem, $inventarioOrdenDescarga->getAlmacenZonaInventario(), $user , true ,$request->request->get('appbundle_inventarioordendescarga')['cantidad']);
            if($resp){

            //   $em->persist($inventarioOrdenDescarga);
            //   $em->flush();
              $this->addFlash(
                  'success',
                  'Item descargado correctamente de inventario'
              );
            }else{
              $this->addFlash(
                  'error',
                  'No cuenta con suficiente stock del material'
              );
            }

            return $this->redirectToRoute('inventarioorden_show', ['id' => $inventarioOrdenItem->getInventarioOrden()->getId()]);
        }
        $total_cantidad = 0;
        $checkCantidades = $em->getRepository('AppBundle:InventarioMovimiento')
            ->createQueryBuilder('a')
            ->where('a.ref1 = :inventarioOrden')
            ->setParameter('inventarioOrden', $inventarioOrdenItem->getInventarioOrden()->getId())
            ->andWhere('a.ref2 = :item')
            ->setParameter('item', $inventarioOrdenItem->getId())
            ->andWhere('a.tipo = :type')
            ->setParameter('type', "EGRESO")
            ->getQuery()
            ->getResult()
            ;
        foreach ($checkCantidades as $item2) {
          $total_cantidad += $item2->getCantidad();
        }
        return $this->render('inventarioordendescarga/new.html.twig', array(
            'inventarioOrdenDescarga' => $inventarioOrdenDescarga,
            'form' => $form->createView(),
            'inventarioOrdenItem' => $inventarioOrdenItem,
            'qty_entregada' => $total_cantidad
        ));
    }

    /**
     * Finds and displays a inventarioOrdenDescarga entity.
     *
     */
    public function showAction(InventarioOrdenDescarga $inventarioOrdenDescarga)
    {
        $deleteForm = $this->createDeleteForm($inventarioOrdenDescarga);

        return $this->render('inventarioordendescarga/show.html.twig', array(
            'inventarioOrdenDescarga' => $inventarioOrdenDescarga,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing inventarioOrdenDescarga entity.
     *
     */
    public function editAction(Request $request, InventarioOrdenDescarga $inventarioOrdenDescarga)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($inventarioOrdenDescarga);
        $editForm = $this->createForm('AppBundle\Form\InventarioOrdenDescargaType', $inventarioOrdenDescarga);
        $editForm->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('inventarioordendescarga_index');
        }

        return $this->render('inventarioordendescarga/edit.html.twig', array(
            'inventarioOrdenDescarga' => $inventarioOrdenDescarga,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a inventarioOrdenDescarga entity.
     *
     */
    public function deleteAction(Request $request, InventarioOrdenDescarga $inventarioOrdenDescarga)
    {
        $form = $this->createDeleteForm($inventarioOrdenDescarga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventarioOrdenDescarga);
            $em->flush($inventarioOrdenDescarga);
        }

        return $this->redirectToRoute('inventarioordendescarga_index');
    }

    /**
     * Creates a form to delete a inventarioOrdenDescarga entity.
     *
     * @param InventarioOrdenDescarga $inventarioOrdenDescarga The inventarioOrdenDescarga entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(InventarioOrdenDescarga $inventarioOrdenDescarga)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventarioordendescarga_delete', array('id' => $inventarioOrdenDescarga->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function eraseAction(InventarioOrdenDescarga $inventarioOrdenDescarga)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($inventarioOrdenDescarga);
        $em->flush($inventarioOrdenDescarga);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('inventarioordendescarga_index');
    }
}
