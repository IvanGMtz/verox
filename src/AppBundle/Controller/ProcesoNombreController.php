<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProcesoNombre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Procesonombre controller.
 *
 */
class ProcesoNombreController extends Controller
{
    /**
     * Lists all procesoNombre entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProcesoNombre'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $procesoNombresQ = $em->getRepository('AppBundle:ProcesoNombre')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProcesoNombre', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $procesoNombresQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $procesoNombresQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $procesoNombresQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $procesoNombres = $pagination->getItems();
        

        return $this->render('procesonombre/index.html.twig', array(
            'procesoNombres' => $procesoNombres,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new procesoNombre entity.
     *
     */
    public function newAction(Request $request)
    {
        $procesoNombre = new Procesonombre();
        $form = $this->createForm('AppBundle\Form\ProcesoNombreType', $procesoNombre);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($procesoNombre);
            $em->flush($procesoNombre);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('procesonombre_index');

        }

        return $this->render('procesonombre/new.html.twig', array(
            'procesoNombre' => $procesoNombre,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a procesoNombre entity.
     *
     */
    public function showAction(ProcesoNombre $procesoNombre)
    {
        $deleteForm = $this->createDeleteForm($procesoNombre);

        return $this->render('procesonombre/show.html.twig', array(
            'procesoNombre' => $procesoNombre,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing procesoNombre entity.
     *
     */
    public function editAction(Request $request, ProcesoNombre $procesoNombre)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($procesoNombre);
        $editForm = $this->createForm('AppBundle\Form\ProcesoNombreType', $procesoNombre);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('procesonombre_index');
        }

        return $this->render('procesonombre/edit.html.twig', array(
            'procesoNombre' => $procesoNombre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a procesoNombre entity.
     *
     */
    public function deleteAction(Request $request, ProcesoNombre $procesoNombre)
    {
        $form = $this->createDeleteForm($procesoNombre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($procesoNombre);
            $em->flush($procesoNombre);
        }

        return $this->redirectToRoute('procesonombre_index');
    }

    /**
     * Creates a form to delete a procesoNombre entity.
     *
     * @param ProcesoNombre $procesoNombre The procesoNombre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProcesoNombre $procesoNombre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('procesonombre_delete', array('id' => $procesoNombre->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProcesoNombre $procesoNombre)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($procesoNombre);
        $em->flush($procesoNombre);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('procesonombre_index');
    }
}
