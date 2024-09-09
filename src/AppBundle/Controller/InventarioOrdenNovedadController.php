<?php

namespace AppBundle\Controller;

use AppBundle\Entity\InventarioOrdenNovedad;
use AppBundle\Entity\InventarioOrden;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Inventarioordennovedad controller.
 *
 */
class InventarioOrdenNovedadController extends Controller
{
    /**
     * Lists all inventarioOrdenNovedad entities.
     *
     */
    public function indexAction(Request $request,inventarioOrden $inventarioOrden ,PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.InventarioOrdenNovedad'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        $params = $request->request->all();
        $inventarioOrdenNovedadsQ = $em->getRepository('AppBundle:InventarioOrdenNovedad')->createQueryBuilder('a')
        ->where('a.inventarioOrden = :order')
        ->setParameter('order', $inventarioOrden);

        if($q && $q !=''){
          $this->get('session')->set('q.InventarioOrdenNovedad', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $inventarioOrdenNovedadsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $inventarioOrdenNovedadsQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }

        $query = $inventarioOrdenNovedadsQ->orderBy('a.id', 'DESC')->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            5000 /*limit per page*/
        );

        $inventarioOrdenNovedads = $pagination->getItems();


        return $this->render('inventarioordennovedad/index.html.twig', array(
            'inventarioOrdenNovedads' => $inventarioOrdenNovedads,
            'q' => $q,
            'pagination' => $pagination,
        ));
    }

    /**
     * Creates a new inventarioOrdenNovedad entity.
     *
     */
    public function newAction(Request $request)
    {
        $inventarioOrdenNovedad = new Inventarioordennovedad();
        $form = $this->createForm('AppBundle\Form\InventarioOrdenNovedadType', $inventarioOrdenNovedad);
        $form->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($inventarioOrdenNovedad);
            $em->flush($inventarioOrdenNovedad);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('inventarioordennovedad_index');

        }

        return $this->render('inventarioordennovedad/new.html.twig', array(
            'inventarioOrdenNovedad' => $inventarioOrdenNovedad,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a inventarioOrdenNovedad entity.
     *
     */
    public function showAction(InventarioOrdenNovedad $inventarioOrdenNovedad)
    {
        $deleteForm = $this->createDeleteForm($inventarioOrdenNovedad);

        return $this->render('inventarioordennovedad/show.html.twig', array(
            'inventarioOrdenNovedad' => $inventarioOrdenNovedad,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing inventarioOrdenNovedad entity.
     *
     */
    public function editAction(Request $request, InventarioOrdenNovedad $inventarioOrdenNovedad)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($inventarioOrdenNovedad);
        $editForm = $this->createForm('AppBundle\Form\InventarioOrdenNovedadType', $inventarioOrdenNovedad);
        $editForm->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('inventarioordennovedad_index');
        }

        return $this->render('inventarioordennovedad/edit.html.twig', array(
            'inventarioOrdenNovedad' => $inventarioOrdenNovedad,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a inventarioOrdenNovedad entity.
     *
     */
    public function deleteAction(Request $request, InventarioOrdenNovedad $inventarioOrdenNovedad)
    {
        $form = $this->createDeleteForm($inventarioOrdenNovedad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventarioOrdenNovedad);
            $em->flush($inventarioOrdenNovedad);
        }

        return $this->redirectToRoute('inventarioordennovedad_index');
    }

    /**
     * Creates a form to delete a inventarioOrdenNovedad entity.
     *
     * @param InventarioOrdenNovedad $inventarioOrdenNovedad The inventarioOrdenNovedad entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(InventarioOrdenNovedad $inventarioOrdenNovedad)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventarioordennovedad_delete', array('id' => $inventarioOrdenNovedad->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function eraseAction(InventarioOrdenNovedad $inventarioOrdenNovedad)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($inventarioOrdenNovedad);
        $em->flush($inventarioOrdenNovedad);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('inventarioordennovedad_index');
    }
}
