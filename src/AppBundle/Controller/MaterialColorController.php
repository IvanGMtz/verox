<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MaterialColor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Materialcolor controller.
 *
 */
class MaterialColorController extends Controller
{
    /**
     * Lists all materialColor entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.MaterialColor'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $materialColorsQ = $em->getRepository('AppBundle:MaterialColor')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.MaterialColor', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $materialColorsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $materialColorsQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $materialColorsQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $materialColors = $pagination->getItems();
        

        return $this->render('materialcolor/index.html.twig', array(
            'materialColors' => $materialColors,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new materialColor entity.
     *
     */
    public function newAction(Request $request)
    {
        $materialColor = new Materialcolor();
        $form = $this->createForm('AppBundle\Form\MaterialColorType', $materialColor);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($materialColor);
            $em->flush($materialColor);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('material_index');

        }

        return $this->render('materialcolor/new.html.twig', array(
            'materialColor' => $materialColor,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a materialColor entity.
     *
     */
    public function showAction(MaterialColor $materialColor)
    {
        $deleteForm = $this->createDeleteForm($materialColor);

        return $this->render('materialcolor/show.html.twig', array(
            'materialColor' => $materialColor,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing materialColor entity.
     *
     */
    public function editAction(Request $request, MaterialColor $materialColor)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($materialColor);
        $editForm = $this->createForm('AppBundle\Form\MaterialColorType', $materialColor);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('materialcolor_index');
        }

        return $this->render('materialcolor/edit.html.twig', array(
            'materialColor' => $materialColor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a materialColor entity.
     *
     */
    public function deleteAction(Request $request, MaterialColor $materialColor)
    {
        $form = $this->createDeleteForm($materialColor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($materialColor);
            $em->flush($materialColor);
        }

        return $this->redirectToRoute('materialcolor_index');
    }

    /**
     * Creates a form to delete a materialColor entity.
     *
     * @param MaterialColor $materialColor The materialColor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MaterialColor $materialColor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('materialcolor_delete', array('id' => $materialColor->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(MaterialColor $materialColor)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($materialColor);
        $em->flush($materialColor);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('materialcolor_index');
    }
}
