<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProduccionNovedad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Produccionnovedad controller.
 *
 */
class ProduccionNovedadController extends Controller
{
    /**
     * Lists all produccionNovedad entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProduccionNovedad'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $produccionNovedadsQ = $em->getRepository('AppBundle:ProduccionNovedad')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProduccionNovedad', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $produccionNovedadsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $produccionNovedadsQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $produccionNovedadsQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $produccionNovedads = $pagination->getItems();
        

        return $this->render('produccionnovedad/index.html.twig', array(
            'produccionNovedads' => $produccionNovedads,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new produccionNovedad entity.
     *
     */
    public function newAction(Request $request)
    {
        $produccionNovedad = new Produccionnovedad();
        $form = $this->createForm('AppBundle\Form\ProduccionNovedadType', $produccionNovedad);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($produccionNovedad);
            $em->flush($produccionNovedad);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('produccionnovedad_index');

        }

        return $this->render('produccionnovedad/new.html.twig', array(
            'produccionNovedad' => $produccionNovedad,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a produccionNovedad entity.
     *
     */
    public function showAction(ProduccionNovedad $produccionNovedad)
    {
        $deleteForm = $this->createDeleteForm($produccionNovedad);

        return $this->render('produccionnovedad/show.html.twig', array(
            'produccionNovedad' => $produccionNovedad,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing produccionNovedad entity.
     *
     */
    public function editAction(Request $request, ProduccionNovedad $produccionNovedad)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($produccionNovedad);
        $editForm = $this->createForm('AppBundle\Form\ProduccionNovedadType', $produccionNovedad);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('produccionnovedad_index');
        }

        return $this->render('produccionnovedad/edit.html.twig', array(
            'produccionNovedad' => $produccionNovedad,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a produccionNovedad entity.
     *
     */
    public function deleteAction(Request $request, ProduccionNovedad $produccionNovedad)
    {
        $form = $this->createDeleteForm($produccionNovedad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produccionNovedad);
            $em->flush($produccionNovedad);
        }

        return $this->redirectToRoute('produccionnovedad_index');
    }

    /**
     * Creates a form to delete a produccionNovedad entity.
     *
     * @param ProduccionNovedad $produccionNovedad The produccionNovedad entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProduccionNovedad $produccionNovedad)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produccionnovedad_delete', array('id' => $produccionNovedad->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProduccionNovedad $produccionNovedad)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($produccionNovedad);
        $em->flush($produccionNovedad);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('produccionnovedad_index');
    }
}
