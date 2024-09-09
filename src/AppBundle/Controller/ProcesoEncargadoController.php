<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProcesoEncargado;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use AppBundle\Entity\Diseno;
use AppBundle\Entity\ProduccionOrden;


/**
 * Procesoencargado controller.
 *
 */
class ProcesoEncargadoController extends Controller
{
    /**
     * Lists all procesoEncargado entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProcesoEncargado'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }

        $procesoEncargadosQ = $em->getRepository('AppBundle:ProcesoEncargado')->createQueryBuilder('a');

        if($q && $q !=''){
          $this->get('session')->set('q.ProcesoEncargado', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $procesoEncargadosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $procesoEncargadosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }

        $query = $procesoEncargadosQ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );

        $procesoEncargados = $pagination->getItems();


        return $this->render('procesoencargado/index.html.twig', array(
            'procesoEncargados' => $procesoEncargados,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new procesoEncargado entity.
     *
     */
    public function newAction(Request $request)
    {
        $procesoEncargado = new Procesoencargado();
        $form = $this->createForm('AppBundle\Form\ProcesoEncargadoType', $procesoEncargado);
        $form->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($procesoEncargado);
            $em->flush($procesoEncargado);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('procesoencargado_index');

        }

        return $this->render('procesoencargado/new.html.twig', array(
            'procesoEncargado' => $procesoEncargado,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a procesoEncargado entity.
     *
     */
    public function showAction(ProcesoEncargado $procesoEncargado)
    {
        $deleteForm = $this->createDeleteForm($procesoEncargado);

        return $this->render('procesoencargado/show.html.twig', array(
            'procesoEncargado' => $procesoEncargado,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing procesoEncargado entity.
     *
     */
    public function editAction(Request $request, ProcesoEncargado $procesoEncargado)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($procesoEncargado);
        $editForm = $this->createForm('AppBundle\Form\ProcesoEncargadoType', $procesoEncargado);
        $editForm->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('diseno_show',['id'=>$procesoEncargado->getDiseno()->getId()]);
        }

        return $this->render('procesoencargado/edit.html.twig', array(
            'procesoEncargado' => $procesoEncargado,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a procesoEncargado entity.
     *
     */
    public function deleteAction(Request $request, ProcesoEncargado $procesoEncargado)
    {
        $form = $this->createDeleteForm($procesoEncargado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($procesoEncargado);
            $em->flush($procesoEncargado);
        }

        return $this->redirectToRoute('procesoencargado_index');
    }

    /**
     * Creates a form to delete a procesoEncargado entity.
     *
     * @param ProcesoEncargado $procesoEncargado The procesoEncargado entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProcesoEncargado $procesoEncargado)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('procesoencargado_delete', array('id' => $procesoEncargado->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function eraseAction(ProcesoEncargado $procesoEncargado)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($procesoEncargado);
        $em->flush($procesoEncargado);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('procesoencargado_index');
    }

    public function terminar_trabajoAction(ProcesoEncargado $procesoEncargado,Diseno $diseno){
      $em = $this->getDoctrine()->getManager();
      $user = $this->container->get('security.token_storage')->getToken()->getUser();
      $ahora = new \DateTime();
      $inicio = $procesoEncargado->getFechaAsignacion();
      $diff = $inicio->diff($ahora);
      $duracion =($diff->d * 1440) + ($diff->h * 60) + ( $diff->i );
      #var_dump($procesoEncargado->getId());exit;
      $procesoEncargado->setFechaFinalizacion($ahora);
      $procesoEncargado->setEnProceso(0);
      $procesoEncargado->setDuracion($duracion);

      $em->persist($procesoEncargado);
      $em->flush();

      $this->addFlash(
        'success',
        'Trabajo terminado correctamente'
      );
      if($procesoEncargado->getProceso()->getTipoOrden() == "DISEÑO" && $procesoEncargado->getProceso()->getProceso() == "BORDADO"){
        return $this->redirectToRoute('diseno_show4', ['id' => $diseno->getId()]);
      }
      else if($procesoEncargado->getProceso()->getTipoOrden() == "DISEÑO" && $procesoEncargado->getProceso()->getProceso() != "BORDADO"){
        return $this->redirectToRoute('diseno_show', ['id' => $diseno->getId()]);
      }
      else if($procesoEncargado->getProceso()->getTipoOrden() == "PRODUCCION" && $procesoEncargado->getProceso()->getProceso() == "BORDADO"){
        return $this->redirectToRoute('diseno_show3', ['diseno' => $diseno->getId(),'produccionOrden'=>$procesoEncargado->getProceso()->getOrden()]);
      }
      return $this->redirectToRoute('diseno_show2', ['diseno' => $diseno->getId(),'produccionOrden'=>$procesoEncargado->getProceso()->getOrden()]);
    }
}
