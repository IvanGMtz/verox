<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MaterialCategoria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Materialcategorium controller.
 *
 */
class MaterialCategoriaController extends Controller
{
    /**
     * Lists all materialCategorium entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.MaterialCategoria'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $materialCategoriasQ = $em->getRepository('AppBundle:MaterialCategoria')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.MaterialCategoria', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $materialCategoriasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $materialCategoriasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $materialCategoriasQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $materialCategorias = $pagination->getItems();
        

        return $this->render('materialcategoria/index.html.twig', array(
            'materialCategorias' => $materialCategorias,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new materialCategorium entity.
     *
     */
    public function newAction(Request $request)
    {
        $materialCategorium = new Materialcategoria();
        $form = $this->createForm('AppBundle\Form\MaterialCategoriaType', $materialCategorium);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($materialCategorium);
            $em->flush($materialCategorium);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('material_index');

        }

        return $this->render('materialcategoria/new.html.twig', array(
            'materialCategorium' => $materialCategorium,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a materialCategorium entity.
     *
     */
    public function showAction(MaterialCategoria $materialCategorium)
    {
        $deleteForm = $this->createDeleteForm($materialCategorium);

        return $this->render('materialcategoria/show.html.twig', array(
            'materialCategorium' => $materialCategorium,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing materialCategorium entity.
     *
     */
    public function editAction(Request $request, MaterialCategoria $materialCategorium)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($materialCategorium);
        $editForm = $this->createForm('AppBundle\Form\MaterialCategoriaType', $materialCategorium);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('materialcategoria_index');
        }

        return $this->render('materialcategoria/edit.html.twig', array(
            'materialCategorium' => $materialCategorium,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a materialCategorium entity.
     *
     */
    public function deleteAction(Request $request, MaterialCategoria $materialCategorium)
    {
        $form = $this->createDeleteForm($materialCategorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($materialCategorium);
            $em->flush($materialCategorium);
        }

        return $this->redirectToRoute('materialcategoria_index');
    }

    /**
     * Creates a form to delete a materialCategorium entity.
     *
     * @param MaterialCategoria $materialCategorium The materialCategorium entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MaterialCategoria $materialCategorium)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('materialcategoria_delete', array('id' => $materialCategorium->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(MaterialCategoria $materialCategorium)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($materialCategorium);
        $em->flush($materialCategorium);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('materialcategoria_index');
    }
}
