<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProduccionDiseno;
use AppBundle\Entity\ProduccionOrden;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Producciondiseno controller.
 *
 */
class ProduccionDisenoController extends Controller
{
    /**
     * Lists all produccionDiseno entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProduccionDiseno'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }

        $produccionDisenosQ = $em->getRepository('AppBundle:ProduccionDiseno')->createQueryBuilder('a');

        if($q && $q !=''){
          $this->get('session')->set('q.ProduccionDiseno', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $produccionDisenosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $produccionDisenosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }

        $query = $produccionDisenosQ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );

        $produccionDisenos = $pagination->getItems();


        return $this->render('producciondiseno/index.html.twig', array(
            'produccionDisenos' => $produccionDisenos,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new produccionDiseno entity.
     *
     */
    public function newAction(Request $request)
    {
        $produccionDiseno = new Producciondiseno();
        $form = $this->createForm('AppBundle\Form\ProduccionDisenoType', $produccionDiseno);
        $form->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($produccionDiseno);
            $em->flush($produccionDiseno);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('producciondiseno_index');

        }

        return $this->render('producciondiseno/new.html.twig', array(
            'produccionDiseno' => $produccionDiseno,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a produccionDiseno entity.
     *
     */
    public function showAction(ProduccionDiseno $produccionDiseno)
    {
        $deleteForm = $this->createDeleteForm($produccionDiseno);

        return $this->render('producciondiseno/show.html.twig', array(
            'produccionDiseno' => $produccionDiseno,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing produccionDiseno entity.
     *
     */
    public function editAction(Request $request, ProduccionDiseno $produccionDiseno)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($produccionDiseno);
        $editForm = $this->createForm('AppBundle\Form\ProduccionDisenoType', $produccionDiseno);
        $editForm->handleRequest($request);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('producciondiseno_index');
        }

        return $this->render('producciondiseno/edit.html.twig', array(
            'produccionDiseno' => $produccionDiseno,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a produccionDiseno entity.
     *
     */
    public function deleteAction(Request $request, ProduccionDiseno $produccionDiseno, ProduccionOrden $produccionOrden)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $tallasProduccion = $em->getRepository('AppBundle:ProduccionTalla')
                ->createQueryBuilder('a')
                ->where('a.diseno = :diseno')
                ->setParameter('diseno', $produccionDiseno->getId())
                ->andwhere('a.ordenProduccion = :orden')
                ->setParameter('orden', $produccionDiseno->getOrdenProduccion()->getId())->getQuery()
                ->getResult();
            foreach ($tallasProduccion as $talla) {
                $em->remove($talla);
                $em->flush();
            }
        
            $em->remove($produccionDiseno);
            $em->flush($produccionDiseno);
            return $this->redirectToRoute('produccionorden_show',['id'=>$produccionOrden->getId()]);
        } catch (\Throwable $th) {
            dump($th);
        }
    }

    /**
     * Creates a form to delete a produccionDiseno entity.
     *
     * @param ProduccionDiseno $produccionDiseno The produccionDiseno entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProduccionDiseno $produccionDiseno)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('producciondiseno_delete', array('id' => $produccionDiseno->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function eraseAction(ProduccionDiseno $produccionDiseno)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($produccionDiseno);
        $em->flush($produccionDiseno);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('producciondiseno_index');
    }
}
