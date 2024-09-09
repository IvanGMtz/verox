<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DisenoImagen;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Disenoimagen controller.
 *
 */
class DisenoImagenController extends Controller
{
    /**
     * Lists all disenoImagen entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.DisenoImagen'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $disenoImagensQ = $em->getRepository('AppBundle:DisenoImagen')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.DisenoImagen', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $disenoImagensQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $disenoImagensQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $disenoImagensQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $disenoImagens = $pagination->getItems();
        

        return $this->render('disenoimagen/index.html.twig', array(
            'disenoImagens' => $disenoImagens,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new disenoImagen entity.
     *
     */
    public function newAction(Request $request)
    {
        $disenoImagen = new Disenoimagen();
        $form = $this->createForm('AppBundle\Form\DisenoImagenType', $disenoImagen);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($disenoImagen);
            $em->flush($disenoImagen);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('disenoimagen_index');

        }

        return $this->render('disenoimagen/new.html.twig', array(
            'disenoImagen' => $disenoImagen,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a disenoImagen entity.
     *
     */
    public function showAction(DisenoImagen $disenoImagen)
    {
        $deleteForm = $this->createDeleteForm($disenoImagen);

        return $this->render('disenoimagen/show.html.twig', array(
            'disenoImagen' => $disenoImagen,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing disenoImagen entity.
     *
     */
    public function editAction(Request $request, DisenoImagen $disenoImagen)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($disenoImagen);
        $editForm = $this->createForm('AppBundle\Form\DisenoImagenType', $disenoImagen);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('disenoimagen_index');
        }

        return $this->render('disenoimagen/edit.html.twig', array(
            'disenoImagen' => $disenoImagen,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a disenoImagen entity.
     *
     */
    public function deleteAction(Request $request, DisenoImagen $disenoImagen)
    {
        $form = $this->createDeleteForm($disenoImagen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($disenoImagen);
            $em->flush($disenoImagen);
        }

        return $this->redirectToRoute('disenoimagen_index');
    }

    /**
     * Creates a form to delete a disenoImagen entity.
     *
     * @param DisenoImagen $disenoImagen The disenoImagen entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DisenoImagen $disenoImagen)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('disenoimagen_delete', array('id' => $disenoImagen->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(DisenoImagen $disenoImagen)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($disenoImagen);
        $em->flush($disenoImagen);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('disenoimagen_index');
    }
}
