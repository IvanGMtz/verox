<?php

namespace AppBundle\Controller;

use AppBundle\Entity\StoreSeo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Storeseo controller.
 *
 */
class StoreSeoController extends Controller
{
    /**
     * Lists all storeSeo entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.StoreSeo'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $storeSeosQ = $em->getRepository('AppBundle:StoreSeo')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.StoreSeo', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $storeSeosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $storeSeosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $storeSeosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $storeSeos = $pagination->getItems();
        

        return $this->render('storeseo/index.html.twig', array(
            'storeSeos' => $storeSeos,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new storeSeo entity.
     *
     */
    public function newAction(Request $request)
    {
        $storeSeo = new Storeseo();
        $form = $this->createForm('AppBundle\Form\StoreSeoType', $storeSeo);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($storeSeo);
            $em->flush($storeSeo);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('storeseo_index');

        }

        return $this->render('storeseo/new.html.twig', array(
            'storeSeo' => $storeSeo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a storeSeo entity.
     *
     */
    public function showAction(StoreSeo $storeSeo)
    {
        $deleteForm = $this->createDeleteForm($storeSeo);

        return $this->render('storeseo/show.html.twig', array(
            'storeSeo' => $storeSeo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing storeSeo entity.
     *
     */
    public function editAction(Request $request, StoreSeo $storeSeo)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($storeSeo);
        $editForm = $this->createForm('AppBundle\Form\StoreSeoType', $storeSeo);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->render('storeseo/edit.html.twig', array(
                'storeSeo' => $storeSeo,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));
        }

        return $this->render('storeseo/edit.html.twig', array(
            'storeSeo' => $storeSeo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a storeSeo entity.
     *
     */
    public function deleteAction(Request $request, StoreSeo $storeSeo)
    {
        $form = $this->createDeleteForm($storeSeo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($storeSeo);
            $em->flush($storeSeo);
        }

        return $this->redirectToRoute('storeseo_index');
    }

    /**
     * Creates a form to delete a storeSeo entity.
     *
     * @param StoreSeo $storeSeo The storeSeo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StoreSeo $storeSeo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('storeseo_delete', array('id' => $storeSeo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(StoreSeo $storeSeo)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($storeSeo);
        $em->flush($storeSeo);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('storeseo_index');
    }
}
