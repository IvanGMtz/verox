<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AlmacenZona;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Entity\Almacen;


/**
 * Almacenzona controller.
 *
 */
class AlmacenZonaController extends Controller
{
    /**
     * Lists all almacenZona entities.
     *
     */
    public function indexAction(Almacen $almacen)
    {
        $em = $this->getDoctrine()->getManager();
        
        $almacenZonasQ = $em->getRepository('AppBundle:AlmacenZona')
                ->createQueryBuilder('a')
                ->where('a.almacen = :almacen')
                ->setParameter('almacen', $almacen)
                ->orderBy('a.ubicacion', 'ASC')
                ;
        $almacenZonas = $almacenZonasQ->getQuery()->getResult();
        return $this->render('almacenzona/index.html.twig', array(
            'almacenZonas' => $almacenZonas,
            'almacen' => $almacen
        ));
    }

    /**
     * Creates a new almacenZona entity.
     *
     */
    public function newAction(Request $request, Almacen $almacen)
    {
        $almacenZona = new Almacenzona();
        $almacenZona->setAlmacen($almacen);
        $form = $this->createForm('AppBundle\Form\AlmacenZonaType', $almacenZona);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $almacenZona->setUsuarioCreacion($user);
            $almacenZona->setFechaCreacion($fecha);
            $em = $this->getDoctrine()->getManager();
            $em->persist($almacenZona);
            $em->flush($almacenZona);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('almacen_show', ['id' => $almacen->getId()]);

        }

        return $this->render('almacenzona/new.html.twig', array(
            'almacenZona' => $almacenZona,
            'form' => $form->createView(),
            'almacen' => $almacen
        ));
    }

    /**
     * Finds and displays a almacenZona entity.
     *
     */
//    public function showAction(AlmacenZona $almacenZona)
//    {
//        $deleteForm = $this->createDeleteForm($almacenZona);
//
//        return $this->render('almacenzona/show.html.twig', array(
//            'almacenZona' => $almacenZona,
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

    /**
     * Displays a form to edit an existing almacenZona entity.
     *
     */
    public function editAction(Request $request, AlmacenZona $almacenZona)
    {
        $em = $this->getDoctrine()->getManager();
//        $deleteForm = $this->createDeleteForm($almacenZona);
        $editForm = $this->createForm('AppBundle\Form\AlmacenZonaType', $almacenZona);
        $editForm->handleRequest($request);
        $almacen = $almacenZona->getAlmacen();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('almacen_show', ['id' => $almacen->getId()]);
        }

        return $this->render('almacenzona/edit.html.twig', array(
            'almacenZona' => $almacenZona,
            'edit_form' => $editForm->createView(),
            'almacen' => $almacen
//            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a almacenZona entity.
     *
     */
//    public function deleteAction(Request $request, AlmacenZona $almacenZona)
//    {
//        $form = $this->createDeleteForm($almacenZona);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($almacenZona);
//            $em->flush($almacenZona);
//        }
//
//        return $this->redirectToRoute('almacenzona_index');
//    }

    /**
     * Creates a form to delete a almacenZona entity.
     *
     * @param AlmacenZona $almacenZona The almacenZona entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createDeleteForm(AlmacenZona $almacenZona)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('almacenzona_delete', array('id' => $almacenZona->getId())))
//            ->setMethod('DELETE')
//            ->getForm()
//        ;
//    }
    
    public function getZonasAction(Almacen $almacen)
    {
        $zonas = [];
        foreach($almacen->getZonas() as $zona){
          array_push($zonas, [
            'id' => $zona->getId(),
            'tag' => $zona->__toString()
          ]);
        }
        return new JsonResponse(['zonas' => $zonas]);
    }
}
