<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProcesoNombre;
use AppBundle\Entity\ProduccionCosto;
use AppBundle\Entity\ProduccionOrden;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Produccioncosto controller.
 *
 */
class ProduccionCostoController extends Controller
{
    /**
     * Lists all produccionCosto entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProduccionCosto'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }

        $produccionCostosQ = $em->getRepository('AppBundle:ProduccionCosto')->createQueryBuilder('a');

        if($q && $q !=''){
          $this->get('session')->set('q.ProduccionCosto', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $produccionCostosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $produccionCostosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }

        $query = $produccionCostosQ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );

        $produccionCostos = $pagination->getItems();


        return $this->render('produccioncosto/index.html.twig', array(
            'produccionCostos' => $produccionCostos,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new produccionCosto entity.
     *
     */
    public function newAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $ordenProduccion = $this->getDoctrine()->getRepository(ProduccionOrden::class)->find($request->query->get('ordenProduccion'));
        $proceso = $this->getDoctrine()->getRepository(ProcesoNombre::class)->find("11");
        $produccionCosto = new Produccioncosto();
        $produccionCosto->setProceso($proceso);
        $produccionCosto->setOrdenProduccion($ordenProduccion);
        $produccionCosto->setCosto($request->query->get('costo'));
        $produccionCosto->setDescripcion($request->query->get('descripcion'));
        $em->persist($produccionCosto);
        $em->flush();
        $this->addFlash(
            'success',
            'Registro creado correctamente'
        );
        return $this->redirectToRoute('produccionorden_show',['id'=>$produccionCosto->getOrdenProduccion()->getId()]);
    }

    /**
     * Finds and displays a produccionCosto entity.
     *
     */
    public function showAction(ProduccionCosto $produccionCosto)
    {
        $deleteForm = $this->createDeleteForm($produccionCosto);

        return $this->render('produccioncosto/show.html.twig', array(
            'produccionCosto' => $produccionCosto,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing produccionCosto entity.
     *
     */
    public function editAction(Request $request, ProduccionCosto $produccionCosto)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($produccionCosto);
        $editForm = $this->createForm('AppBundle\Form\ProduccionCostoType', $produccionCosto);
        $editForm->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('produccionorden_show',['id'=>$produccionCosto->getOrdenProduccion()->getId()]);
        }

        return $this->render('produccioncosto/edit.html.twig', array(
            'produccionCosto' => $produccionCosto,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a produccionCosto entity.
     *
     */
    public function deleteAction(Request $request, ProduccionCosto $produccionCosto)
    {
        $form = $this->createDeleteForm($produccionCosto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produccionCosto);
            $em->flush($produccionCosto);
        }

        return $this->redirectToRoute('produccioncosto_index');
    }

    /**
     * Creates a form to delete a produccionCosto entity.
     *
     * @param ProduccionCosto $produccionCosto The produccionCosto entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProduccionCosto $produccionCosto)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produccioncosto_delete', array('id' => $produccionCosto->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function eraseAction(ProduccionCosto $produccionCosto)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($produccionCosto);
        $em->flush($produccionCosto);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('produccioncosto_index');
    }
}
