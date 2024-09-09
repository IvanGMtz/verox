<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DisenoBordado;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Disenobordado controller.
 *
 */
class DisenoBordadoController extends Controller
{
    /**
     * Lists all disenoBordado entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.DisenoBordado'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $disenoBordadosQ = $em->getRepository('AppBundle:DisenoBordado')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.DisenoBordado', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $disenoBordadosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $disenoBordadosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $disenoBordadosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $disenoBordados = $pagination->getItems();
        

        return $this->render('disenobordado/index.html.twig', array(
            'disenoBordados' => $disenoBordados,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new disenoBordado entity.
     *
     */
    public function newAction(Request $request)
    {
        $disenoBordado = new Disenobordado();
        $form = $this->createForm('AppBundle\Form\DisenoBordadoType', $disenoBordado);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($disenoBordado);
            $em->flush($disenoBordado);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('disenobordado_index');

        }

        return $this->render('disenobordado/new.html.twig', array(
            'disenoBordado' => $disenoBordado,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a disenoBordado entity.
     *
     */
    public function showAction(DisenoBordado $disenoBordado)
    {
        $deleteForm = $this->createDeleteForm($disenoBordado);

        return $this->render('disenobordado/show.html.twig', array(
            'disenoBordado' => $disenoBordado,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing disenoBordado entity.
     *
     */
    public function editAction(Request $request, DisenoBordado $disenoBordado)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($disenoBordado);
        $editForm = $this->createForm('AppBundle\Form\DisenoBordadoType', $disenoBordado);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('disenobordado_index');
        }

        return $this->render('disenobordado/edit.html.twig', array(
            'disenoBordado' => $disenoBordado,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a disenoBordado entity.
     *
     */
    public function deleteAction(Request $request, DisenoBordado $disenoBordado)
    {
        $form = $this->createDeleteForm($disenoBordado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($disenoBordado);
            $em->flush($disenoBordado);
        }

        return $this->redirectToRoute('disenobordado_index');
    }

    /**
     * Creates a form to delete a disenoBordado entity.
     *
     * @param DisenoBordado $disenoBordado The disenoBordado entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DisenoBordado $disenoBordado)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('disenobordado_delete', array('id' => $disenoBordado->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(DisenoBordado $disenoBordado)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($disenoBordado);
        $em->flush($disenoBordado);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('disenobordado_index');
    }
}
