<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Producto;
use AppBundle\Entity\ProductoComplemento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Productocomplemento controller.
 *
 */
class ProductoComplementoController extends Controller
{
    /**
     * Lists all productoComplemento entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.ProductoComplemento'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $productoComplementosQ = $em->getRepository('AppBundle:ProductoComplemento')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.ProductoComplemento', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $productoComplementosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $productoComplementosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $productoComplementosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $productoComplementos = $pagination->getItems();
        

        return $this->render('productocomplemento/index.html.twig', array(
            'productoComplementos' => $productoComplementos,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new productoComplemento entity.
     *
     */
    public function newAction(Request $request)
    {
        $productoComplemento = new Productocomplemento();
        $form = $this->createForm('AppBundle\Form\ProductoComplementoType', $productoComplemento);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();
        $producto = $request->query->get('producto');
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $this->getDoctrine()->getRepository(Producto::class)->find($producto);
            $em = $this->getDoctrine()->getManager();
            $productoComplemento->setProducto($product);
            $em->persist($productoComplemento);
            $em->flush($productoComplemento);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('productocomplemento_index');

        }

        return $this->render('productocomplemento/new.html.twig', array(
            'productoComplemento' => $productoComplemento,
            'form' => $form->createView(),
            'producto'=>$producto
        ));
    }

    /**
     * Finds and displays a productoComplemento entity.
     *
     */
    public function showAction(ProductoComplemento $productoComplemento)
    {
        $deleteForm = $this->createDeleteForm($productoComplemento);

        return $this->render('productocomplemento/show.html.twig', array(
            'productoComplemento' => $productoComplemento,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing productoComplemento entity.
     *
     */
    public function editAction(Request $request, ProductoComplemento $productoComplemento)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($productoComplemento);
        $editForm = $this->createForm('AppBundle\Form\ProductoComplementoType', $productoComplemento);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('productocomplemento_index');
        }

        return $this->render('productocomplemento/edit.html.twig', array(
            'productoComplemento' => $productoComplemento,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a productoComplemento entity.
     *
     */
    public function deleteAction(Request $request, ProductoComplemento $productoComplemento)
    {
        $form = $this->createDeleteForm($productoComplemento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productoComplemento);
            $em->flush($productoComplemento);
        }

        return $this->redirectToRoute('productocomplemento_index');
    }

    /**
     * Creates a form to delete a productoComplemento entity.
     *
     * @param ProductoComplemento $productoComplemento The productoComplemento entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProductoComplemento $productoComplemento)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productocomplemento_delete', array('id' => $productoComplemento->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(ProductoComplemento $productoComplemento)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($productoComplemento);
        $em->flush($productoComplemento);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('productocomplemento_index');
    }
}
