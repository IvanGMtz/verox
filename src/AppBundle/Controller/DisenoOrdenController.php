<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DisenoOrden;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Disenoorden controller.
 *
 */
class DisenoOrdenController extends Controller
{
    /**
     * Lists all disenoOrden entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.DisenoOrden'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }

        $disenoOrdensQ = $em->getRepository('AppBundle:DisenoOrden')->createQueryBuilder('a');

        if($q && $q !=''){
          $this->get('session')->set('q.DisenoOrden', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $disenoOrdensQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $disenoOrdensQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }

        $query = $disenoOrdensQ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );

        $disenoOrdens = $pagination->getItems();


        return $this->render('disenoorden/index.html.twig', array(
            'disenoOrdens' => $disenoOrdens,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new disenoOrden entity.
     *
     */
    public function newAction(Request $request)
    {
        $disenoOrden = new Disenoorden();
        $form = $this->createForm('AppBundle\Form\DisenoOrdenType', $disenoOrden);
        $form->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($disenoOrden);
            $em->flush($disenoOrden);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('disenoorden_index');

        }

        return $this->render('disenoorden/new.html.twig', array(
            'disenoOrden' => $disenoOrden,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a disenoOrden entity.
     *
     */
    public function showAction(DisenoOrden $disenoOrden)
    {
        $deleteForm = $this->createDeleteForm($disenoOrden);

        return $this->render('disenoorden/show.html.twig', array(
            'disenoOrden' => $disenoOrden,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing disenoOrden entity.
     *
     */
    public function editAction(Request $request, DisenoOrden $disenoOrden)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($disenoOrden);
        $editForm = $this->createForm('AppBundle\Form\DisenoOrdenType', $disenoOrden);
        $editForm->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('disenoorden_index');
        }

        return $this->render('disenoorden/edit.html.twig', array(
            'disenoOrden' => $disenoOrden,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a disenoOrden entity.
     *
     */
    public function deleteAction(Request $request, DisenoOrden $disenoOrden)
    {
        $form = $this->createDeleteForm($disenoOrden);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $procesos = $em->getRepository('AppBundle:Proceso')
            ->createQueryBuilder('a')
            ->where('a.orden = :order')
            ->setParameter('order', $disenoOrden->getId())
            ->andwhere('a.tipoOrden = :tipo')
            ->setParameter('tipo', 'DISEÑO')->getQuery()
            ->getResult();
            foreach ($procesos as $proceso) {
                $em->remove($proceso);
                $em->flush();
            }
            $em->remove($disenoOrden);
            $em->flush($disenoOrden);
        }

        return $this->redirectToRoute('disenoorden_index');
    }

    /**
     * Creates a form to delete a disenoOrden entity.
     *
     * @param DisenoOrden $disenoOrden The disenoOrden entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DisenoOrden $disenoOrden)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('disenoorden_delete', array('id' => $disenoOrden->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function eraseAction(DisenoOrden $disenoOrden)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($disenoOrden);
        $em->flush($disenoOrden);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('disenoorden_index');
    }
}
