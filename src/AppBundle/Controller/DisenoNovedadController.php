<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DisenoNovedad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Disenonovedad controller.
 *
 */
class DisenoNovedadController extends Controller
{
    /**
     * Lists all disenoNovedad entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.DisenoNovedad'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $disenoNovedadsQ = $em->getRepository('AppBundle:DisenoNovedad')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.DisenoNovedad', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $disenoNovedadsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $disenoNovedadsQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $disenoNovedadsQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $disenoNovedads = $pagination->getItems();
        

        return $this->render('disenonovedad/index.html.twig', array(
            'disenoNovedads' => $disenoNovedads,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new disenoNovedad entity.
     *
     */
    public function newAction(Request $request)
    {
        $disenoNovedad = new Disenonovedad();
        $form = $this->createForm('AppBundle\Form\DisenoNovedadType', $disenoNovedad);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($disenoNovedad);
            $em->flush($disenoNovedad);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('disenonovedad_index');

        }

        return $this->render('disenonovedad/new.html.twig', array(
            'disenoNovedad' => $disenoNovedad,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a disenoNovedad entity.
     *
     */
    public function showAction(DisenoNovedad $disenoNovedad)
    {
        $deleteForm = $this->createDeleteForm($disenoNovedad);

        return $this->render('disenonovedad/show.html.twig', array(
            'disenoNovedad' => $disenoNovedad,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing disenoNovedad entity.
     *
     */
    public function editAction(Request $request, DisenoNovedad $disenoNovedad)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($disenoNovedad);
        $editForm = $this->createForm('AppBundle\Form\DisenoNovedadType', $disenoNovedad);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('disenonovedad_index');
        }

        return $this->render('disenonovedad/edit.html.twig', array(
            'disenoNovedad' => $disenoNovedad,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a disenoNovedad entity.
     *
     */
    public function deleteAction(Request $request, DisenoNovedad $disenoNovedad)
    {
        $form = $this->createDeleteForm($disenoNovedad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($disenoNovedad);
            $em->flush($disenoNovedad);
        }

        return $this->redirectToRoute('disenonovedad_index');
    }

    /**
     * Creates a form to delete a disenoNovedad entity.
     *
     * @param DisenoNovedad $disenoNovedad The disenoNovedad entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DisenoNovedad $disenoNovedad)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('disenonovedad_delete', array('id' => $disenoNovedad->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(DisenoNovedad $disenoNovedad)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($disenoNovedad);
        $em->flush($disenoNovedad);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('disenonovedad_index');
    }
}
