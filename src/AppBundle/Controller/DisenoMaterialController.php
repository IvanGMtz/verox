<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DisenoMaterial;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Disenomaterial controller.
 *
 */
class DisenoMaterialController extends Controller
{
    /**
     * Lists all disenoMaterial entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.DisenoMaterial'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $disenoMaterialsQ = $em->getRepository('AppBundle:DisenoMaterial')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.DisenoMaterial', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $disenoMaterialsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $disenoMaterialsQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $disenoMaterialsQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $disenoMaterials = $pagination->getItems();
        

        return $this->render('disenomaterial/index.html.twig', array(
            'disenoMaterials' => $disenoMaterials,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new disenoMaterial entity.
     *
     */
    public function newAction(Request $request)
    {
        $disenoMaterial = new Disenomaterial();
        $form = $this->createForm('AppBundle\Form\DisenoMaterialType', $disenoMaterial);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($disenoMaterial);
            $em->flush($disenoMaterial);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('disenomaterial_index');

        }

        return $this->render('disenomaterial/new.html.twig', array(
            'disenoMaterial' => $disenoMaterial,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a disenoMaterial entity.
     *
     */
    public function showAction(DisenoMaterial $disenoMaterial)
    {
        $deleteForm = $this->createDeleteForm($disenoMaterial);

        return $this->render('disenomaterial/show.html.twig', array(
            'disenoMaterial' => $disenoMaterial,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing disenoMaterial entity.
     *
     */
    public function editAction(Request $request, DisenoMaterial $disenoMaterial)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($disenoMaterial);
        $editForm = $this->createForm('AppBundle\Form\DisenoMaterialType', $disenoMaterial);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('disenomaterial_index');
        }

        return $this->render('disenomaterial/edit.html.twig', array(
            'disenoMaterial' => $disenoMaterial,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a disenoMaterial entity.
     *
     */
    public function deleteAction(Request $request, DisenoMaterial $disenoMaterial)
    {
        $form = $this->createDeleteForm($disenoMaterial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($disenoMaterial);
            $em->flush($disenoMaterial);
        }

        return $this->redirectToRoute('disenomaterial_index');
    }

    /**
     * Creates a form to delete a disenoMaterial entity.
     *
     * @param DisenoMaterial $disenoMaterial The disenoMaterial entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DisenoMaterial $disenoMaterial)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('disenomaterial_delete', array('id' => $disenoMaterial->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(DisenoMaterial $disenoMaterial)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($disenoMaterial);
        $em->flush($disenoMaterial);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('disenomaterial_index');
    }
}
