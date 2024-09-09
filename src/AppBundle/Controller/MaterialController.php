<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Material;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Knp\Component\Pager\PaginatorInterface;

/**
 * Material controller.
 *
 */
class MaterialController extends Controller
{
    /**
     * Lists all material entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.material'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $materialsQ = $em->getRepository('AppBundle:Material')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.material', $q);
          $qcount = 0;
          foreach($q as $field => $value){
  
            if($value){
              if($qcount == 0){
                if(strpos($field, 'equal') !== false){
                  $title = explode('-', $field);
                  $materialsQ->where('a.'.$title[0].' = :'.$title[0])->setParameter($title[0], $value);
                }else{
                  $materialsQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
              }else{
                if(strpos($field, 'equal') !== false){
                  $title = explode('-', $field);
                  $materialsQ->andWhere('a.'.$title[0].' = :'.$title[0])->setParameter($title[0], $value);
                }else{
                  $materialsQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
              }
            }
            $qcount++;
          }
        }
        
        $query = $materialsQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $materials = $pagination->getItems();

        return $this->render('material/index.html.twig', array(
            'materials' => $materials,
            'q' => $q,
            'pagination' => $pagination,
            'categorias' => $em->getRepository('AppBundle:MaterialCategoria')->findAll()
        ));
    }

    /**
     * Creates a new material entity.
     *
     */
    public function newAction(Request $request)
    {
        $material = new Material();
        $form = $this->createForm('AppBundle\Form\MaterialType', $material);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ref = preg_replace('/[^a-zA-Z0-9]/', '', $material->getPresentacion());
            $ref = strtoupper(str_replace(' ', '', $ref));
            $checkref = $em->getRepository('AppBundle:Material')
                    ->createQueryBuilder('a')
                    ->where('a.nombre = :nombre')
                    ->setParameter('nombre', $material->getNombre()->getNombre())
                    ->andwhere('a.color = :color')
                    ->setParameter('color', $material->getColor()->getNombre())
                    ->andwhere('a.medida = :medida')
                    ->setParameter('medida', $material->getMedida()->getNombre())
                    ->andwhere('a.presentacion = :referencia')
                    ->setParameter('referencia', $ref)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
            if(!is_null($checkref)){
              $this->addFlash(
                'error',
                'El material ya existe'
              );
              return $this->render('material/new.html.twig', array(
                  'material' => $material,
                  'form' => $form->createView(),
              ));
            }
            
            $material->setUsuarioCreacion($user);
            $material->setPresentacion($ref);
            $material->setReferencia('temp');
            $material->setFechaCreacion($fecha);
            $material->setEstado(1);
            
            $em->persist($material);
            $em->flush($material);
            $material->setReferencia($material->getId());
            $em->flush($material);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('material_index');

        }

        return $this->render('material/new.html.twig', array(
            'material' => $material,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a material entity.
     *
     */
    public function showAction(Material $material)
    {
        $deleteForm = $this->createDeleteForm($material);

        return $this->render('material/show.html.twig', array(
            'material' => $material,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing material entity.
     *
     */
    public function editAction(Request $request, Material $material)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($material);
        $nombre = $em->getRepository('AppBundle:MaterialNombre')
                    ->createQueryBuilder('a')
                    ->where('a.nombre = :nombre')
                    ->setParameter('nombre', $material->getNombre())
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
        $color = $em->getRepository('AppBundle:MaterialColor')
                    ->createQueryBuilder('a')
                    ->where('a.nombre = :nombre')
                    ->setParameter('nombre', $material->getColor())
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
        $medida = $em->getRepository('AppBundle:MaterialMedida')
                    ->createQueryBuilder('a')
                    ->where('a.nombre = :nombre')
                    ->setParameter('nombre', $material->getMedida())
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
        $material->setNombre($nombre);
        $material->setColor($color);
        $material->setMedida($medida);
        $editForm = $this->createForm('AppBundle\Form\MaterialType', $material);
        $editForm->handleRequest($request);
        // dump($editForm);exit;
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $material->setFechaActualizacion($fecha);
            
            $checkref = $em->getRepository('AppBundle:Material')
                    ->createQueryBuilder('a')
                    ->where('a.presentacion = :referencia')
                    ->setParameter('referencia', $material->getPresentacion())
                    ->andWhere('a.id <> :id')
                    ->setParameter('id', $material->getId())
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
            
            if(!is_null($checkref)){
              $this->addFlash(
                'error',
                'La referencia ingresada ya existe'
              );
              return $this->render('material/edit.html.twig', array(
                  'material' => $material,
                  'edit_form' => $editForm->createView(),
                  'delete_form' => $deleteForm->createView(),
              ));
            }
            
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('material_index');
        }
        return $this->render('material/edit.html.twig', array(
            'material' => $material,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a material entity.
     *
     */
    public function deleteAction(Request $request, Material $material)
    {
        $form = $this->createDeleteForm($material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($material);
            $em->flush($material);
        }

        return $this->redirectToRoute('material_index');
    }

    /**
     * Creates a form to delete a material entity.
     *
     * @param Material $material The material entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Material $material)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('material_delete', array('id' => $material->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(Material $material)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($material);
        $em->flush($material);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('material_index');
    }
}
