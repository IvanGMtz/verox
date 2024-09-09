<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProduccionTalla;
use AppBundle\Entity\ProduccionOrden;
use AppBundle\Entity\ProduccionDiseno;
use AppBundle\Entity\Diseno;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Producciontalla controller.
 *
 */
class ProduccionTallaController extends Controller
{
    /**
     * Lists all produccionTalla entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProduccionTalla'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }

        $produccionTallasQ = $em->getRepository('AppBundle:ProduccionTalla')->createQueryBuilder('a');

        if($q && $q !=''){
          $this->get('session')->set('q.ProduccionTalla', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $produccionTallasQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $produccionTallasQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }

        $query = $produccionTallasQ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );

        $produccionTallas = $pagination->getItems();


        return $this->render('producciontalla/index.html.twig', array(
            'produccionTallas' => $produccionTallas,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new produccionTalla entity.
     *
     */
    public function newAction(Request $request, ProduccionOrden $produccionOrden)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $produccionOrden->setEstado(0);
        $fecha = new \DateTime();
        $diseno = $this->getDoctrine()->getRepository(Diseno::class)->find($request->request->get('diseno'));
        $disenoP = new ProduccionDiseno();
        $disenoP->setDiseno($diseno);
        $disenoP->setCantidad($request->request->get('total'));
        $disenoP->setOrdenProduccion($produccionOrden);
        $disenoP->setFechaCreacion($fecha);
        $disenoP->setEstado(0);
        $em->persist($disenoP);
        $em->flush();
        $params = $request->request->all();
        foreach ($params as $key => $param) {
          if(strpos($key, 'Q-') !== false && $request->request->get($key) != "0" ){
            $index = explode("-",$key)[1];
            $produccionTalla = new Producciontalla();
            $produccionTalla->setTalla($request->request->get('T-'.$index));
            $produccionTalla->setCantidad((int)$request->request->get('Q-'.$index));
            $produccionTalla->setDiseno($disenoP);
            $produccionTalla->setCantidadConfirmada((int)$request->request->get('Q-'.$index));
            $produccionTalla->setOrdenProduccion($produccionOrden);
            $em->persist($produccionTalla);
            $em->flush();
          }
        }
        $this->addFlash(
            'success',
            'Diseño agregado correctamente'
        );
        return $this->redirectToRoute('produccionorden_show',['id'=>$produccionOrden->getId()]);
    }

    /**
     * Finds and displays a produccionTalla entity.
     *
     */
    public function showAction(ProduccionTalla $produccionTalla)
    {
        $deleteForm = $this->createDeleteForm($produccionTalla);

        return $this->render('producciontalla/show.html.twig', array(
            'produccionTalla' => $produccionTalla,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing produccionTalla entity.
     *
     */
    public function editAction(Request $request, ProduccionTalla $produccionTalla)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($produccionTalla);
        $editForm = $this->createForm('AppBundle\Form\ProduccionTallaType', $produccionTalla);
        $editForm->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('producciontalla_index');
        }

        return $this->render('producciontalla/edit.html.twig', array(
            'produccionTalla' => $produccionTalla,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a produccionTalla entity.
     *
     */
    public function deleteAction(Request $request, ProduccionTalla $produccionTalla)
    {
        $form = $this->createDeleteForm($produccionTalla);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produccionTalla);
            $em->flush($produccionTalla);
        }

        return $this->redirectToRoute('producciontalla_index');
    }

    /**
     * Creates a form to delete a produccionTalla entity.
     *
     * @param ProduccionTalla $produccionTalla The produccionTalla entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProduccionTalla $produccionTalla)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('producciontalla_delete', array('id' => $produccionTalla->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function eraseAction(ProduccionTalla $produccionTalla)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($produccionTalla);
        $em->flush($produccionTalla);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('producciontalla_index');
    }
}
