<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProduccionCostoMaterial;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Produccioncostomaterial controller.
 *
 */
class ProduccionCostoMaterialController extends Controller
{
    /**
     * Lists all produccionCostoMaterial entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProduccionCostoMaterial'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $produccionCostoMaterialsQ = $em->getRepository('AppBundle:ProduccionCostoMaterial')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProduccionCostoMaterial', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $produccionCostoMaterialsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $produccionCostoMaterialsQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $produccionCostoMaterialsQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $produccionCostoMaterials = $pagination->getItems();
        

        return $this->render('produccioncostomaterial/index.html.twig', array(
            'produccionCostoMaterials' => $produccionCostoMaterials,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new produccionCostoMaterial entity.
     *
     */
    public function newAction(Request $request)
    {
        $produccionCostoMaterial = new Produccioncostomaterial();
        $form = $this->createForm('AppBundle\Form\ProduccionCostoMaterialType', $produccionCostoMaterial);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($produccionCostoMaterial);
            $em->flush($produccionCostoMaterial);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('produccioncostomaterial_index');

        }

        return $this->render('produccioncostomaterial/new.html.twig', array(
            'produccionCostoMaterial' => $produccionCostoMaterial,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a produccionCostoMaterial entity.
     *
     */
    public function showAction(ProduccionCostoMaterial $produccionCostoMaterial)
    {
        $deleteForm = $this->createDeleteForm($produccionCostoMaterial);

        return $this->render('produccioncostomaterial/show.html.twig', array(
            'produccionCostoMaterial' => $produccionCostoMaterial,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing produccionCostoMaterial entity.
     *
     */
    public function editAction(Request $request, ProduccionCostoMaterial $produccionCostoMaterial)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($produccionCostoMaterial);
        $editForm = $this->createForm('AppBundle\Form\ProduccionCostoMaterialType', $produccionCostoMaterial);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('produccioncostomaterial_index');
        }

        return $this->render('produccioncostomaterial/edit.html.twig', array(
            'produccionCostoMaterial' => $produccionCostoMaterial,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a produccionCostoMaterial entity.
     *
     */
    public function deleteAction(Request $request, ProduccionCostoMaterial $produccionCostoMaterial)
    {
        $form = $this->createDeleteForm($produccionCostoMaterial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produccionCostoMaterial);
            $em->flush($produccionCostoMaterial);
        }

        return $this->redirectToRoute('produccioncostomaterial_index');
    }

    /**
     * Creates a form to delete a produccionCostoMaterial entity.
     *
     * @param ProduccionCostoMaterial $produccionCostoMaterial The produccionCostoMaterial entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProduccionCostoMaterial $produccionCostoMaterial)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produccioncostomaterial_delete', array('id' => $produccionCostoMaterial->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProduccionCostoMaterial $produccionCostoMaterial)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($produccionCostoMaterial);
        $em->flush($produccionCostoMaterial);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('produccioncostomaterial_index');
    }
}
