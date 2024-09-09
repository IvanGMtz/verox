<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DocumentoEtiqueta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Documentoetiquetum controller.
 *
 */
class DocumentoEtiquetaController extends Controller
{
    /**
     * Lists all documentoEtiquetum entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.DocumentoEtiqueta'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $documentoEtiquetasQ = $em->getRepository('AppBundle:DocumentoEtiqueta')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.DocumentoEtiqueta', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $documentoEtiquetasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $documentoEtiquetasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $documentoEtiquetasQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $documentoEtiquetas = $pagination->getItems();
        

        return $this->render('documentoetiqueta/index.html.twig', array(
            'documentoEtiquetas' => $documentoEtiquetas,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new documentoEtiquetum entity.
     *
     */
    public function newAction(Request $request)
    {
        $documentoEtiquetum = new Documentoetiquetum();
        $form = $this->createForm('AppBundle\Form\DocumentoEtiquetaType', $documentoEtiquetum);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($documentoEtiquetum);
            $em->flush($documentoEtiquetum);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('documentoetiqueta_index');

        }

        return $this->render('documentoetiqueta/new.html.twig', array(
            'documentoEtiquetum' => $documentoEtiquetum,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a documentoEtiquetum entity.
     *
     */
    public function showAction(DocumentoEtiqueta $documentoEtiquetum)
    {
        $deleteForm = $this->createDeleteForm($documentoEtiquetum);

        return $this->render('documentoetiqueta/show.html.twig', array(
            'documentoEtiquetum' => $documentoEtiquetum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing documentoEtiquetum entity.
     *
     */
    public function editAction(Request $request, DocumentoEtiqueta $documentoEtiquetum)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($documentoEtiquetum);
        $editForm = $this->createForm('AppBundle\Form\DocumentoEtiquetaType', $documentoEtiquetum);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('documentoetiqueta_index');
        }

        return $this->render('documentoetiqueta/edit.html.twig', array(
            'documentoEtiquetum' => $documentoEtiquetum,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a documentoEtiquetum entity.
     *
     */
    public function deleteAction(Request $request, DocumentoEtiqueta $documentoEtiquetum)
    {
        $form = $this->createDeleteForm($documentoEtiquetum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($documentoEtiquetum);
            $em->flush($documentoEtiquetum);
        }

        return $this->redirectToRoute('documentoetiqueta_index');
    }

    /**
     * Creates a form to delete a documentoEtiquetum entity.
     *
     * @param DocumentoEtiqueta $documentoEtiquetum The documentoEtiquetum entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DocumentoEtiqueta $documentoEtiquetum)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('documentoetiqueta_delete', array('id' => $documentoEtiquetum->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(DocumentoEtiqueta $documentoEtiquetum)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($documentoEtiquetum);
        $em->flush($documentoEtiquetum);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('documentoetiqueta_index');
    }
}
