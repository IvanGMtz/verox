<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MaterialMedida;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Materialmedida controller.
 *
 */
class MaterialMedidaController extends Controller
{
    /**
     * Lists all materialMedida entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.MaterialMedida'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $materialMedidasQ = $em->getRepository('AppBundle:MaterialMedida')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.MaterialMedida', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $materialMedidasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $materialMedidasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $materialMedidasQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $materialMedidas = $pagination->getItems();
        

        return $this->render('materialmedida/index.html.twig', array(
            'materialMedidas' => $materialMedidas,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new materialMedida entity.
     *
     */
    public function newAction(Request $request)
    {
        $materialMedida = new Materialmedida();
        $form = $this->createForm('AppBundle\Form\MaterialMedidaType', $materialMedida);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($materialMedida);
            $em->flush($materialMedida);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('material_index');
        }

        return $this->render('materialmedida/new.html.twig', array(
            'materialMedida' => $materialMedida,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a materialMedida entity.
     *
     */
    public function showAction(MaterialMedida $materialMedida)
    {
        $deleteForm = $this->createDeleteForm($materialMedida);

        return $this->render('materialmedida/show.html.twig', array(
            'materialMedida' => $materialMedida,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing materialMedida entity.
     *
     */
    public function editAction(Request $request, MaterialMedida $materialMedida)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($materialMedida);
        $editForm = $this->createForm('AppBundle\Form\MaterialMedidaType', $materialMedida);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('materialmedida_index');
        }

        return $this->render('materialmedida/edit.html.twig', array(
            'materialMedida' => $materialMedida,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a materialMedida entity.
     *
     */
    public function deleteAction(Request $request, MaterialMedida $materialMedida)
    {
        $form = $this->createDeleteForm($materialMedida);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($materialMedida);
            $em->flush($materialMedida);
        }

        return $this->redirectToRoute('materialmedida_index');
    }

    /**
     * Creates a form to delete a materialMedida entity.
     *
     * @param MaterialMedida $materialMedida The materialMedida entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MaterialMedida $materialMedida)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('materialmedida_delete', array('id' => $materialMedida->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(MaterialMedida $materialMedida)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($materialMedida);
        $em->flush($materialMedida);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('materialmedida_index');
    }
}
