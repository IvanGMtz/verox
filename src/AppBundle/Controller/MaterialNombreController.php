<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MaterialNombre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Materialnombre controller.
 *
 */
class MaterialNombreController extends Controller
{
    /**
     * Lists all materialNombre entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.MaterialNombre'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $materialNombresQ = $em->getRepository('AppBundle:MaterialNombre')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.MaterialNombre', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $materialNombresQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $materialNombresQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $materialNombresQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $materialNombres = $pagination->getItems();
        

        return $this->render('materialnombre/index.html.twig', array(
            'materialNombres' => $materialNombres,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new materialNombre entity.
     *
     */
    public function newAction(Request $request)
    {
        $materialNombre = new Materialnombre();
        $form = $this->createForm('AppBundle\Form\MaterialNombreType', $materialNombre);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($materialNombre);
            $em->flush($materialNombre);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('material_index');
        }
        return $this->render('materialnombre/new.html.twig', array(
            'materialNombre' => $materialNombre,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a materialNombre entity.
     *
     */
    public function showAction(MaterialNombre $materialNombre)
    {
        $deleteForm = $this->createDeleteForm($materialNombre);

        return $this->render('materialnombre/show.html.twig', array(
            'materialNombre' => $materialNombre,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing materialNombre entity.
     *
     */
    public function editAction(Request $request, MaterialNombre $materialNombre)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($materialNombre);
        $editForm = $this->createForm('AppBundle\Form\MaterialNombreType', $materialNombre);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('materialnombre_index');
        }

        return $this->render('materialnombre/edit.html.twig', array(
            'materialNombre' => $materialNombre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a materialNombre entity.
     *
     */
    public function deleteAction(Request $request, MaterialNombre $materialNombre)
    {
        $form = $this->createDeleteForm($materialNombre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($materialNombre);
            $em->flush($materialNombre);
        }

        return $this->redirectToRoute('materialnombre_index');
    }

    /**
     * Creates a form to delete a materialNombre entity.
     *
     * @param MaterialNombre $materialNombre The materialNombre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MaterialNombre $materialNombre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('materialnombre_delete', array('id' => $materialNombre->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(MaterialNombre $materialNombre)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($materialNombre);
        $em->flush($materialNombre);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('materialnombre_index');
    }
}
