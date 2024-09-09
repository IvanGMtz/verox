<?php

namespace AppBundle\Controller;

use AppBundle\Entity\StoreBonos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Storebono controller.
 *
 */
class StoreBonosController extends Controller
{
    /**
     * Lists all storeBono entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.StoreBonos'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $storeBonosQ = $em->getRepository('AppBundle:StoreBonos')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.StoreBonos', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $storeBonosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $storeBonosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $storeBonosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $storeBonos = $pagination->getItems();
        

        return $this->render('storebonos/index.html.twig', array(
            'storeBonos' => $storeBonos,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new storeBono entity.
     *
     */
    public function newAction(Request $request)
    {
        $storeBono = new StoreBonos();
        $form = $this->createForm('AppBundle\Form\StoreBonosType', $storeBono);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($storeBono);
            $em->flush($storeBono);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('storebonos_index');

        }

        return $this->render('storebonos/new.html.twig', array(
            'storeBono' => $storeBono,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a storeBono entity.
     *
     */
    public function showAction(StoreBonos $storeBono)
    {
        $deleteForm = $this->createDeleteForm($storeBono);

        return $this->render('storebonos/show.html.twig', array(
            'storeBono' => $storeBono,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing storeBono entity.
     *
     */
    public function editAction(Request $request, StoreBonos $storeBono)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($storeBono);
        $editForm = $this->createForm('AppBundle\Form\StoreBonosType', $storeBono);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('storebonos_index');
        }

        return $this->render('storebonos/edit.html.twig', array(
            'storeBono' => $storeBono,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a storeBono entity.
     *
     */
    public function deleteAction(Request $request, StoreBonos $storeBono)
    {
        $form = $this->createDeleteForm($storeBono);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($storeBono);
            $em->flush($storeBono);
        }

        return $this->redirectToRoute('storebonos_index');
    }

    /**
     * Creates a form to delete a storeBono entity.
     *
     * @param StoreBonos $storeBono The storeBono entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StoreBonos $storeBono)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('storebonos_delete', array('id' => $storeBono->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(StoreBonos $storeBono)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($storeBono);
        $em->flush($storeBono);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('storebonos_index');
    }
}
