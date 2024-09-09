<?php

namespace AppBundle\Controller;

use AppBundle\Entity\StoreUsuarios;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * Storeusuario controller.
 *
 */
class StoreUsuariosController extends Controller
{
    /**
     * Lists all storeUsuario entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.StoreUsuarios'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $storeUsuariosQ = $em->getRepository('AppBundle:StoreUsuarios')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.StoreUsuarios', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $storeUsuariosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $storeUsuariosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        $query = $storeUsuariosQ->orderBy('a.id', 'DESC')->getQuery();
        
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            50 /*limit per page*/
        );
        
        $storeUsuarios = $pagination->getItems();
        

        return $this->render('storeusuarios/index.html.twig', array(
            'storeUsuarios' => $storeUsuarios,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new storeUsuario entity.
     *
     */
    public function newAction(Request $request)
    {
        $storeUsuario = new Storeusuarios();
        $form = $this->createForm('AppBundle\Form\StoreUsuariosType', $storeUsuario);
        $form->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($storeUsuario);
            $em->flush($storeUsuario);
            $this->addFlash(
                'success',
                'Registro creado correctamente'
            );
            return $this->redirectToRoute('storeusuarios_index');

        }

        return $this->render('storeusuarios/new.html.twig', array(
            'storeUsuario' => $storeUsuario,
            'form' => $form->createView(),
        ));
    }
    /**
     * Finds and displays a storeUsuario entity.
     *
     */
    public function showAction(StoreUsuarios $storeUsuario)
    {
        $deleteForm = $this->createDeleteForm($storeUsuario);

        return $this->render('storeusuarios/show.html.twig', array(
            'storeUsuario' => $storeUsuario,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing storeUsuario entity.
     *
     */
    public function editAction(Request $request, StoreUsuarios $storeUsuario)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($storeUsuario);
        $editForm = $this->createForm('AppBundle\Form\StoreUsuariosType', $storeUsuario);
        $editForm->handleRequest($request);
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $fecha = new \DateTime();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            $client = new Client();
            $client->post("https://www.veroxcloset.com/api/update_user",
            ['form_params' => [
                "json_string" => json_encode([
                    "email"=>$storeUsuario->getEmail(),
                    "nombre"=>$storeUsuario->getNombre(),
                    "apellidos"=>$storeUsuario->getApellidos(),
                    "telefono"=>$storeUsuario->getTelefono(),
                    "direccion"=>$storeUsuario->getDireccion(),
                    "tipo"=>$storeUsuario->getTipo(),
                    "webId"=>$storeUsuario->getWebId(),
                ])]]);
            return $this->redirectToRoute('storeusuarios_index');
        }

        return $this->render('storeusuarios/edit.html.twig', array(
            'storeUsuario' => $storeUsuario,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a storeUsuario entity.
     *
     */
    public function deleteAction(Request $request, StoreUsuarios $storeUsuario)
    {
        $form = $this->createDeleteForm($storeUsuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Borrar usuario de la tienda principal
            $client = new Client();
            $client->post("https://www.veroxcloset.com/api/delete_user",
            ['form_params' => [
                "json_string" => json_encode([
                    "email"=>$storeUsuario->getEmail()
                ])]]);
            $em = $this->getDoctrine()->getManager();
            $em->remove($storeUsuario);
            $em->flush($storeUsuario);
        }

        return $this->redirectToRoute('storeusuarios_index');
    }

    /**
     * Creates a form to delete a storeUsuario entity.
     *
     * @param StoreUsuarios $storeUsuario The storeUsuario entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StoreUsuarios $storeUsuario)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('storeusuarios_delete', array('id' => $storeUsuario->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(StoreUsuarios $storeUsuario)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($storeUsuario);
        $em->flush($storeUsuario);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('storeusuarios_index');
    }
    public function reportAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('AppBundle:StoreUsuarios')->findAll();
        // Solicita el servicio de excel
       $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

       $phpExcelObject->getProperties()->setCreator("Admin")
           ->setLastModifiedBy("Admin")
           ->setTitle("Office 2005 XLSX Test Document")
           ->setSubject("Office 2005 XLSX Test Document")
           ->setDescription("Usuarios Tienda")
           ->setKeywords("office 2005 openxml php")
           ->setCategory("Usuarios");
        $row = 2;
        foreach ($data as $item) {
            $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', "ID")
            ->setCellValue('A'.$row, (string)$item->getId())
            ->setCellValue('B1', "EMAIL")
            ->setCellValue('B'.$row, (string)$item->getEmail())
            ->setCellValue('C1', "NOMBRE")
            ->setCellValue('C'.$row, (string)$item->getNombre())
            ->setCellValue('D1', "APELLIDOS")
            ->setCellValue('D'.$row, (string)$item->getApellidos())
            ->setCellValue('E1', "TELEFONO")
            ->setCellValue('E'.$row, (string)$item->getTelefono())
            ->setCellValue('F1', "FECHA NACIMIENTO")
            ->setCellValue('F'.$row, (string)$item->getBirthday())
            ->setCellValue('G1', "DIRECCION")
            ->setCellValue('G'.$row, (string)$item->getDireccion())
            ->setCellValue('H1', "ESTADO")
            ->setCellValue('H'.$row, (string)$item->getEstado())
            ->setCellValue('I1', "TIPO")
            ->setCellValue('I'.$row, (string)$item->getTipo())
            ->setCellValue('J1', "ASESOR")
            ->setCellValue('J'.$row, (string)$item->getAsesor())
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
            'usuarios_tienda.xlsx'
        );

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;  
    }
}
