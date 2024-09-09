<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProcesoNotas;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Procesonota controller.
 *
 */
class ProcesoNotasController extends Controller
{
    /**
     * Lists all procesoNota entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProcesoNotas'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $procesoNotasQ = $em->getRepository('AppBundle:ProcesoNotas')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProcesoNotas', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $procesoNotasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $procesoNotasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $procesoNotasQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $procesoNotas = $pagination->getItems();
        

        return $this->render('procesonotas/index.html.twig', array(
            'procesoNotas' => $procesoNotas,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new procesoNota entity.
     *
     */
    public function newAction(Request $request)
    {
        $procesoNota = new Procesonota();
        $form = $this->createForm('AppBundle\Form\ProcesoNotasType', $procesoNota);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($procesoNota);
            $em->flush($procesoNota);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('procesonotas_index');

        }

        return $this->render('procesonotas/new.html.twig', array(
            'procesoNota' => $procesoNota,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a procesoNota entity.
     *
     */
    public function showAction(ProcesoNotas $procesoNota)
    {
        $deleteForm = $this->createDeleteForm($procesoNota);

        return $this->render('procesonotas/show.html.twig', array(
            'procesoNota' => $procesoNota,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing procesoNota entity.
     *
     */
    public function editAction(Request $request, ProcesoNotas $procesoNota)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($procesoNota);
        $editForm = $this->createForm('AppBundle\Form\ProcesoNotasType', $procesoNota);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('procesonotas_index');
        }

        return $this->render('procesonotas/edit.html.twig', array(
            'procesoNota' => $procesoNota,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a procesoNota entity.
     *
     */
    public function deleteAction(Request $request, ProcesoNotas $procesoNota)
    {
        $form = $this->createDeleteForm($procesoNota);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($procesoNota);
            $em->flush($procesoNota);
        }

        return $this->redirectToRoute('procesonotas_index');
    }

    /**
     * Creates a form to delete a procesoNota entity.
     *
     * @param ProcesoNotas $procesoNota The procesoNota entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProcesoNotas $procesoNota)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('procesonotas_delete', array('id' => $procesoNota->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProcesoNotas $procesoNota)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($procesoNota);
        $em->flush($procesoNota);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('procesonotas_index');
    }
}
