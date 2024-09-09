<?php

namespace AppBundle\Controller;

use AppBundle\Entity\InventarioCosto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * Inventariocosto controller.
 *
 */
class InventarioCostoController extends Controller
{
    /**
     * Lists all inventarioCosto entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.InventarioCosto'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $inventarioCostosQ = $em->getRepository('AppBundle:InventarioCosto')->createQueryBuilder('a')
        ->join('a.material','m')
        ->join('a.zona','z')
        ->join('a.proveedor','p')
        ->join('m.categoria','c')
        ;
        
        if($q && $q !=''){
          $this->get('session')->set('q.InventarioCosto', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                if($field=="material"){
                    $inventarioCostosQ->where('CONCAT(m.nombre, \' \',m.color, \' \',m.medida, \' \',m.marca) LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="zona") {
                    $inventarioCostosQ->where('z.ubicacion LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="proveedor") {
                    $inventarioCostosQ->where('p.nombre LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="categoria") {
                    $inventarioCostosQ->where('c.nombre LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                else{
                    $inventarioCostosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
              }else{
                if($field=="material"){
                    $inventarioCostosQ->andwhere('m.nombre LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="zona") {
                    $inventarioCostosQ->andwhere('z.ubicacion LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="proveedor") {
                    $inventarioCostosQ->andwhere('p.nombre LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="categoria") {
                    $inventarioCostosQ->andwhere('c.nombre LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                else{
                    $inventarioCostosQ->andwhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
              }
              $qcount++;
            }
          }
        }
        
        $query = $inventarioCostosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $inventarioCostos = $pagination;
        
        $Costos = $em->getRepository('AppBundle:InventarioCosto')->findAll();
        $total_sin_iva = 0;
        $total_con_iva = 0;
        foreach ($Costos as $item) {
            $total_sin_iva += $item->getCantidadActual() * $item->getValorSinIva();
            $total_con_iva += $item->getCantidadActual() * $item->getValorConIva();
        }

        return $this->render('inventariocosto/index.html.twig', array(
            'inventarioCostos' => $inventarioCostos,
            'q' => $q,
            'pagination' => $pagination,
            'totalSinIva' => $total_sin_iva,
            'totalConIva' => $total_con_iva,
        ));
    }
    public function index2Action(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.InventarioCosto'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $inventarioCostosQ = $em->getRepository('AppBundle:InventarioCosto')->createQueryBuilder('a')
        ->join('a.material','m')
        ->join('a.zona','z')
        ->join('a.proveedor','p')
        ->join('m.categoria','c')
        ;
        
        if($q && $q !=''){
          $this->get('session')->set('q.InventarioCosto', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                if($field=="material"){
                    $inventarioCostosQ->where('CONCAT(m.nombre, \' \',m.color, \' \',m.medida, \' \',m.marca) LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="zona") {
                    $inventarioCostosQ->where('z.ubicacion LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="proveedor") {
                    $inventarioCostosQ->where('p.nombre LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="categoria") {
                    $inventarioCostosQ->where('c.nombre LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                else{
                    $inventarioCostosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
              }else{
                if($field=="material"){
                    $inventarioCostosQ->andwhere('m.nombre LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="zona") {
                    $inventarioCostosQ->andwhere('z.ubicacion LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="proveedor") {
                    $inventarioCostosQ->andwhere('p.nombre LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                elseif ($field=="categoria") {
                    $inventarioCostosQ->andwhere('c.nombre LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
                else{
                    $inventarioCostosQ->andwhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
                }
              }
              $qcount++;
            }
          }
        }
        
        $query = $inventarioCostosQ->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $inventarioCostos = $pagination->getItems();
        

        return $this->render('inventariocosto/index2.html.twig', array(
            'inventarioCostos' => $inventarioCostos,
            'q' => $q,
            'pagination' => $pagination
        ));
    }
    /**
     * Creates a new inventarioCosto entity.
     *
     */
    public function newAction(Request $request)
    {
        $inventarioCosto = new Inventariocosto();
        $form = $this->createForm('AppBundle\Form\InventarioCostoType', $inventarioCosto);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($inventarioCosto);
            $em->flush($inventarioCosto);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('inventariocosto_index');

        }

        return $this->render('inventariocosto/new.html.twig', array(
            'inventarioCosto' => $inventarioCosto,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a inventarioCosto entity.
     *
     */
    public function showAction(InventarioCosto $inventarioCosto)
    {
        $deleteForm = $this->createDeleteForm($inventarioCosto);

        return $this->render('inventariocosto/show.html.twig', array(
            'inventarioCosto' => $inventarioCosto,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing inventarioCosto entity.
     *
     */
    public function editAction(Request $request, InventarioCosto $inventarioCosto)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($inventarioCosto);
        $editForm = $this->createForm('AppBundle\Form\InventarioCostoType', $inventarioCosto);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('inventariocosto_index');
        }

        return $this->render('inventariocosto/edit.html.twig', array(
            'inventarioCosto' => $inventarioCosto,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a inventarioCosto entity.
     *
     */
    public function deleteAction(Request $request, InventarioCosto $inventarioCosto)
    {
        $form = $this->createDeleteForm($inventarioCosto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventarioCosto);
            $em->flush($inventarioCosto);
        }

        return $this->redirectToRoute('inventariocosto_index');
    }

    /**
     * Creates a form to delete a inventarioCosto entity.
     *
     * @param InventarioCosto $inventarioCosto The inventarioCosto entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(InventarioCosto $inventarioCosto)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventariocosto_delete', array('id' => $inventarioCosto->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(InventarioCosto $inventarioCosto)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($inventarioCosto);
        $em->flush($inventarioCosto);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('inventariocosto_index');
    }

    public function reportAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('AppBundle:InventarioCosto')->findAll();
        // Solicita el servicio de excel
       $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

       $phpExcelObject->getProperties()->setCreator("Admin")
           ->setLastModifiedBy("Admin")
           ->setTitle("Office 2005 XLSX Test Document")
           ->setSubject("Office 2005 XLSX Test Document")
           ->setDescription("Reporte de costo de Inventario")
           ->setKeywords("office 2005 openxml php")
           ->setCategory("Costo de Inventario");
        $row = 2;
        foreach ($data as $item) {
            $nombre = $item->getMaterial()->getNombre();
            if($item->getMaterial()->getColor()){$nombre .= ' '.$item->getMaterial()->getColor();}
            if($item->getMaterial()->getDescripcion()){$nombre .= ' '.$item->getMaterial()->getDescripcion();}
            if($item->getMaterial()->getMarca() != 'NO APLICA'){$nombre .= ' '.$item->getMaterial()->getMedida().' MARCA: '.$item->getMaterial()->getMarca();}
            $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', "MATERIAL")
            ->setCellValue('A'.$row, $nombre)
            ->setCellValue('B1', "CATEGORIA")
            ->setCellValue('B'.$row, $item->getMaterial()->getCategoria()->getNombre())
            ->setCellValue('C1', "ZONA")
            ->setCellValue('C'.$row, $item->getZona()->getUbicacion().' - '.$item->getZona()->getEstante())
            ->setCellValue('D1', "PROVEEDOR")
            ->setCellValue('D'.$row, $item->getProveedor()->getNombre())
            ->setCellValue('E1', "TOTAL INGRESO")
            ->setCellValue('E'.$row, (string)$item->getIngreso())
            ->setCellValue('F1', "TOTAL EGRESO")
            ->setCellValue('F'.$row, (string)$item->getEgreso())
            ->setCellValue('G1', "CANTIDAD ACTUAL")
            ->setCellValue('G'.$row, (string)$item->getCantidadActual())
            ->setCellValue('H1', "VALOR SIN IVA")
            ->setCellValue('H'.$row, (string)$item->getValorSinIva())
            ->setCellValue('I1', "VALOR IVA")
            ->setCellValue('I'.$row, (string)(floatval($item->getValorConIva()) - floatval($item->getValorSinIva())))
            ->setCellValue('J1', "TOTAL")
            ->setCellValue('J'.$row, (string)$item->getValorConIva())
            ;
            $row++;
        }
       
       $phpExcelObject->getActiveSheet()->setTitle('Simple');
       // Define el indice de página al número 1, para abrir esa página al abrir el archivo
       $phpExcelObject->setActiveSheetIndex(0);

        // Crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        // Envia la respuesta del controlador
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // Agrega los headers requeridos
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'costo_inventario.xlsx'
        );

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;  
    }
}
