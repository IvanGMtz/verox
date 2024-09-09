<?php

namespace AppBundle\Controller;

use AppBundle\Entity\StoreTiendaSlider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Storetiendaslider controller.
 *
 */
class StoreTiendaSliderController extends Controller
{
    /**
     * Lists all storeTiendaSlider entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.StoreTiendaSlider'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $storeTiendaSlidersQ = $em->getRepository('AppBundle:StoreTiendaSlider')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.StoreTiendaSlider', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $storeTiendaSlidersQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $storeTiendaSlidersQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $storeTiendaSlidersQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $storeTiendaSliders = $pagination->getItems();
        

        return $this->render('storetiendaslider/index.html.twig', array(
            'storeTiendaSliders' => $storeTiendaSliders,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new storeTiendaSlider entity.
     *
     */
    public function newAction(Request $request)
    {
        $storeTiendaSlider = new Storetiendaslider();
        $form = $this->createForm('AppBundle\Form\StoreTiendaSliderType', $storeTiendaSlider);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($storeTiendaSlider);
            $em->flush($storeTiendaSlider);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('storetiendaslider_index');

        }

        return $this->render('storetiendaslider/new.html.twig', array(
            'storeTiendaSlider' => $storeTiendaSlider,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a storeTiendaSlider entity.
     *
     */
    public function showAction(StoreTiendaSlider $storeTiendaSlider)
    {
        $deleteForm = $this->createDeleteForm($storeTiendaSlider);

        return $this->render('storetiendaslider/show.html.twig', array(
            'storeTiendaSlider' => $storeTiendaSlider,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing storeTiendaSlider entity.
     *
     */
    public function editAction(Request $request, StoreTiendaSlider $storeTiendaSlider)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($storeTiendaSlider);
        $editForm = $this->createForm('AppBundle\Form\StoreTiendaSliderType', $storeTiendaSlider);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('storetiendaslider_index');
        }

        return $this->render('storetiendaslider/edit.html.twig', array(
            'storeTiendaSlider' => $storeTiendaSlider,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a storeTiendaSlider entity.
     *
     */
    public function deleteAction(Request $request, StoreTiendaSlider $storeTiendaSlider)
    {
        $form = $this->createDeleteForm($storeTiendaSlider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($storeTiendaSlider);
            $em->flush($storeTiendaSlider);
        }

        return $this->redirectToRoute('storetiendaslider_index');
    }

    /**
     * Creates a form to delete a storeTiendaSlider entity.
     *
     * @param StoreTiendaSlider $storeTiendaSlider The storeTiendaSlider entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StoreTiendaSlider $storeTiendaSlider)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('storetiendaslider_delete', array('id' => $storeTiendaSlider->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(StoreTiendaSlider $storeTiendaSlider)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($storeTiendaSlider);
        $em->flush($storeTiendaSlider);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('storetiendaslider_index');
    }
}
