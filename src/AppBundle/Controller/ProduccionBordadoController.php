<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProduccionBordado;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Produccionbordado controller.
 *
 */
class ProduccionBordadoController extends Controller
{
    /**
     * Lists all produccionBordado entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProduccionBordado'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $produccionBordadosQ = $em->getRepository('AppBundle:ProduccionBordado')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProduccionBordado', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $produccionBordadosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $produccionBordadosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $produccionBordadosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $produccionBordados = $pagination->getItems();
        

        return $this->render('produccionbordado/index.html.twig', array(
            'produccionBordados' => $produccionBordados,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new produccionBordado entity.
     *
     */
    public function newAction(Request $request)
    {
        $produccionBordado = new Produccionbordado();
        $form = $this->createForm('AppBundle\Form\ProduccionBordadoType', $produccionBordado);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($produccionBordado);
            $em->flush($produccionBordado);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('produccionbordado_index');

        }

        return $this->render('produccionbordado/new.html.twig', array(
            'produccionBordado' => $produccionBordado,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a produccionBordado entity.
     *
     */
    public function showAction(ProduccionBordado $produccionBordado)
    {
        $deleteForm = $this->createDeleteForm($produccionBordado);

        return $this->render('produccionbordado/show.html.twig', array(
            'produccionBordado' => $produccionBordado,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing produccionBordado entity.
     *
     */
    public function editAction(Request $request, ProduccionBordado $produccionBordado)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($produccionBordado);
        $editForm = $this->createForm('AppBundle\Form\ProduccionBordadoType', $produccionBordado);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('produccionbordado_index');
        }

        return $this->render('produccionbordado/edit.html.twig', array(
            'produccionBordado' => $produccionBordado,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a produccionBordado entity.
     *
     */
    public function deleteAction(Request $request, ProduccionBordado $produccionBordado)
    {
        $form = $this->createDeleteForm($produccionBordado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produccionBordado);
            $em->flush($produccionBordado);
        }

        return $this->redirectToRoute('produccionbordado_index');
    }

    /**
     * Creates a form to delete a produccionBordado entity.
     *
     * @param ProduccionBordado $produccionBordado The produccionBordado entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProduccionBordado $produccionBordado)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produccionbordado_delete', array('id' => $produccionBordado->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProduccionBordado $produccionBordado)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($produccionBordado);
        $em->flush($produccionBordado);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('produccionbordado_index');
    }
}
