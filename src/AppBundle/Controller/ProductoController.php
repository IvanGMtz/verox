<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Producto;
use AppBundle\Entity\ProductoColor;
use AppBundle\Entity\ProductoComplemento;
use AppBundle\Entity\ProductoDescripcion;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

use function PHPUnit\Framework\isNull;

/**
 * Producto controller.
 *
 */
class ProductoController extends Controller
{
    /**
     * Lists all producto entities.
     *
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $request->request->get('q', $this->get('session')->get('q.Producto'));
        if ($request->isMethod('POST')) {
          $page = 1;
        }else{
          $page = $request->query->getInt('page', 1);
        }
        
        $productosQ = $em->getRepository('AppBundle:Producto')->createQueryBuilder('a');
        
        if($q && $q !=''){
          $this->get('session')->set('q.Producto', $q);
          $qcount = 0;
          foreach($q as $field => $value){
            if($value){
              if($qcount == 0){
                $productosQ->where('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }else{
                $productosQ->andWhere('a.'.$field.' LIKE :'.$field)->setParameter($field, '%'.$value.'%');
              }
            }
            $qcount++;
          }
        }
        
        $query = $productosQ->orderBy('a.id', 'DESC')->getQuery();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            1000 /*limit per page*/
        );
        
        $productos = $pagination->getItems();
        

        return $this->render('producto/index.html.twig', array(
            'productos' => $productos,
            'q' => $q,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new producto entity.
     *
     */
    public function newAction(Request $request)
    {
        try {
            $producto = new Producto();
            $form = $this->createForm('AppBundle\Form\ProductoType', $producto);
            $form->handleRequest($request);
            
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $fecha = new \DateTime();

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($producto);
                $em->flush($producto);
                $description = new ProductoDescripcion();
                $description->setTexto($request->request->get('texto'));
                //$description->setTextEn($request->request->get('texto_en'));
                $description->setProducto($producto);
                $em->persist($description);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Registro creado correctamente'
                );
                return $this->redirectToRoute('producto_index');
            }

            return $this->render('producto/new.html.twig', array(
                'producto' => $producto,
                'form' => $form->createView(),
            ));
        } catch (\Throwable $th) {
            dump($th);
        }
        
    }

    /**
     * Finds and displays a producto entity.
     *
     */
    public function showAction(Producto $producto)
    {
        $deleteForm = $this->createDeleteForm($producto);
        $em = $this->getDoctrine()->getManager();
        $tallas = $em->getRepository('AppBundle:ProductoTalla')
                ->createQueryBuilder('a')
                ->where('a.producto = :product')
                ->setParameter('product', $producto)
                ->getQuery()
                ->getResult();
        $color = $em->getRepository('AppBundle:ProductoColor')
                ->createQueryBuilder('a')
                ->where('a.producto = :product')
                ->setParameter('product', $producto)
                ->getQuery()
                ->getResult();
        $imagen = $em->getRepository('AppBundle:ProductoImagen')
                ->createQueryBuilder('a')
                ->where('a.producto = :product')
                ->setParameter('product', $producto)
                ->getQuery()
                ->getResult();
        $cantidad = $em->getRepository('AppBundle:ProductoInventario')
                ->createQueryBuilder('a')
                ->join('a.producto','p')
                ->where('p.producto = :product')
                ->setParameter('product', $producto)
                ->getQuery()
                ->getResult();
        $descripcion = $em->getRepository('AppBundle:ProductoDescripcion')
                ->createQueryBuilder('a')
                ->where('a.producto = :product')
                ->setParameter('product', $producto)
                ->getQuery()
                ->getResult();
        $complementos_array = [];
        $complementos = $em->getRepository('AppBundle:ProductoComplemento')
                ->createQueryBuilder('a')
                ->where('a.producto = :product')
                ->setParameter('product', $producto)
                ->getQuery()
                ->getResult();
        foreach ($complementos as $producto_c) {
            $complemento = $em->getRepository(Producto::class)->find($producto_c->getComplemento()->getId());
            array_push($complementos_array,$complemento);
        }
        return $this->render('producto/show.html.twig', array(
            'producto' => $producto,
            'delete_form' => $deleteForm->createView(),
            'tallas' => $tallas,
            'color' => $color,
            'imagenes' => $imagen,
            'cantidad' => $cantidad,
            'descripcion' => $descripcion,
            'complementarios' => $complementos_array,
        ));
    }

    /**
     * Displays a form to edit an existing producto entity.
     *
     */
    public function editAction(Request $request, Producto $producto)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($producto);
        $descripcion = $em->getRepository('AppBundle:ProductoDescripcion')
                ->createQueryBuilder('a')
                ->where('a.producto = :product')
                ->setParameter('product', $producto)
                ->getQuery()
                ->getResult();
        $originalComplementos = new ArrayCollection();
        $originalColores = new ArrayCollection();
        $originalTallas = new ArrayCollection();
        $originalImagenes = new ArrayCollection();
        foreach ($producto->getComplementos() as $complemento) {$originalComplementos->add($complemento);}
        foreach ($producto->getColores() as $color) {$originalColores->add($color);}
        foreach ($producto->getTallas() as $talla) {$originalTallas->add($talla);}
        foreach ($producto->getImagenes() as $imagen) {$originalImagenes->add($imagen);}
        $editForm = $this->createForm('AppBundle\Form\ProductoType', $producto);
        $editForm->handleRequest($request);
        //dump($originalComplementos);exit;
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            //dump($originalImagenes);exit;
            $descript = $this->getDoctrine()->getRepository(ProductoDescripcion::class)->findOneBy(array('producto' => $producto));
            if($descript == null){
                $description = new ProductoDescripcion();
                $description->setTexto($request->request->get('texto'));
                $description->setTextoEn($request->request->get('texto_en'));
                $description->setProducto($producto);
                $em->persist($description);
                $em->flush();
            }
            else{
                $descript->setTexto($request->request->get('texto'));
                $descript->setTextoEn($request->request->get('texto_en'));
            }
            foreach ($originalComplementos as $item) {
                if (false === $producto->getComplementos()->contains($item)) {
                    $em->remove($item); 
                }
            }
            foreach ($originalColores as $item) {
                if ($item->getNombre()==null) {
                  $em->remove($item);
                }
            }
            foreach ($originalTallas as $item) {
                if ($item->getNombre()==null) {
                    $em->remove($item);
                }
            }
            foreach ($originalImagenes as $item) {
                if ($item->getOrden()==null) {
                  $em->remove($item);
                }
            }
            $em->flush();
            $this->addFlash(
                'success',
                'Registro editado correctamente'
            );
            return $this->redirectToRoute('producto_index');
        }
        return $this->render('producto/edit.html.twig', array(
            'producto' => $producto,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'descripcion' => $descripcion,
        ));
    }

    public function copyAction(Request $request, Producto $producto)
    {
        $em = $this->getDoctrine()->getManager();
        $newProduct = clone $producto;
        $newProduct->setReferencia($producto->getReferencia()." - Copia");
        $em->persist($newProduct);
        $em->flush();
        $descript = $this->getDoctrine()->getRepository(ProductoDescripcion::class)->findOneBy(array('producto' => $producto));
        if($descript){
            $newDescription = clone $descript;
            $newDescription->setProducto($newProduct);
            $em->persist($newDescription);
            $em->flush();
        }
        foreach ($producto->getComplementos() as $complemento) {
            $newComplemento =clone $complemento;
            $newComplemento->setProducto($newProduct);
            $em->persist($newComplemento);
            $em->flush();
        }
        foreach ($producto->getColores() as $color) {
            $newColor = clone $color;
            $newColor->setProducto($newProduct);
            $em->persist($newColor);
            $em->flush();
        }
        foreach ($producto->getTallas() as $talla) {
            $newTalla = clone $talla;
            $newTalla->setProducto($newProduct);
            $em->persist($newTalla);
            $em->flush();
        }
        // foreach ($producto->getImagenes() as $imagen) {
        //     $newImagen = clone $imagen;
        //     $newImagen->setProducto($newProduct);
        //     $em->persist($newImagen);
        //     $em->flush();
        // }
        $this->addFlash(
            'success',
            'Registro duplicado correctamente'
        );
        return $this->redirectToRoute('producto_index');
    }

    /**
     * Deletes a producto entity.
     *
     */
    public function deleteAction(Request $request, Producto $producto)
    {
        $form = $this->createDeleteForm($producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($producto);
            $em->flush($producto);
        }

        return $this->redirectToRoute('producto_index');
    }

    /**
     * Creates a form to delete a producto entity.
     *
     * @param Producto $producto The producto entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Producto $producto)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('producto_delete', array('id' => $producto->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    public function eraseAction(Producto $producto)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($producto);
        $em->flush($producto);
        $this->addFlash(
            'success',
            'Registro eliminado correctamente'
        );
        return $this->redirectToRoute('producto_index');
    }

    public function search_productAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            //dump($request->query->get('ref'));exit;
            $referencia = explode("-",$request->query->get('ref'))[0];
            $talla_str = explode("-",$request->query->get('ref'))[1];
            $color_str = count(explode("-",$request->query->get('ref'))) > 2 ? explode("-",$request->query->get('ref'))[2] : null;
            $product = $em->getRepository('AppBundle:Producto')
                    ->createQueryBuilder('a')
                    ->where('a.referencia = :ref')
                    ->setParameter('ref', $referencia)
                    ->getQuery()
                    ->getResult();
            $talla = $em->getRepository('AppBundle:ProductoTalla')
                    ->createQueryBuilder('a')
                    ->where('a.producto = :product')
                    ->setParameter('product', $product[0]->getId())
                    ->andwhere('a.nombre = :nom')
                    ->setParameter('nom', $talla_str)
                    ->getQuery()
                    ->getResult();
            $colores_array=[];
            $query = $em->getRepository('AppBundle:ProductoColor')
                    ->createQueryBuilder('a')
                    ->where('a.producto = :product')
                    ->setParameter('product', $product[0]->getId());
            if($color_str){
                $query->andWhere('a.nombre = :name')
                ->setParameter('name', $color_str);
            }
            $colores = $query->getQuery()
            ->getResult();
            foreach ($colores as $color) {
                $existencia = $em->getRepository('AppBundle:ProductoInventario')->findOneBy(array('producto' => $talla,'color' => $color));
                $reserva_detal = 0;
                $reserva_mayorista = 0;
                $reservados = $em->getRepository('AppBundle:DespachoOrdenItem')
                    ->createQueryBuilder('a')
                    ->where('a.producto = :producto')
                    ->setParameter('producto', $talla)
                    ->andwhere('a.color = :color')
                    ->setParameter('color', $color->getNombre())
                    ->andwhere('a.estatus = :estatus')
                    ->setParameter('estatus', 1)
                    ->getQuery()
                    ->getResult();
                foreach ($reservados as $reservado) {
                    if($reservado->getBodega()=="DETAL"){
                        $reserva_detal = $reserva_detal + $reservado->getCantidad();
                    }
                    else{
                        $reserva_mayorista = $reserva_mayorista + $reservado->getCantidad();
                    }
                }
                $qtyMayoristas = 0;
                if($existencia!=null){
                    $qtyMayoristas = ($existencia->getQtyActualMayorista()- $reserva_mayorista<0)?0:$existencia->getQtyActualMayorista() - $reserva_mayorista;
                }
                array_push($colores_array,[
                    "nombre" => $color->getNombre(),
                    "hex" => $color->getHex(),
                    "id" => $color->getId(),
                    "producto_id" => $talla[0]->getId(),
                    "DETAL"=>($existencia!=null)?(($qtyMayoristas <= 0)?0:$existencia->getQtyActualDetal() - $reserva_detal):0,
                    "MAYORISTA"=>($existencia!=null)?$existencia->getQtyActualMayorista()-$reserva_mayorista:0,
                ]);
            }
            if($product && $talla && $color){
                $data = [
                    "request"=>"success",
                    "producto_id"=>$product[0]->getId(),
                    "producto_referencia" => $product[0]->getReferencia(),
                    "producto_nombre" => $product[0]->getNombre(),
                    "producto_talla" => $talla[0]->getNombre(),
                    "producto_talla_id" => $talla[0]->getId(),
                    "producto_color" => $colores_array,
                    "producto_precio_detal" => $product[0]->getPrecioDetal(),
                    "producto_precio_mayorista" => $product[0]->getPrecioMayorista(),
                ];
                $response = new Response(json_encode($data));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else{
                $response = new Response(json_encode(["request"=>"error"]));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        } catch (\Throwable $th) {
            dump($th);exit;
            $response = new Response(json_encode(["error"=>$th]));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        
    }

    public function change_etiquetasAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        try {
            $changes = json_decode($request->getContent(),true);
            $changesEtiquetas = $changes["etiquetas"];
            $changesEstados = $changes["estados"];
            foreach ($changesEtiquetas as $key => $value) {
                $product = $em->getRepository(Producto::class)->find($key);
                if($product != NULL){
                    $product->setEtiqueta($value);
                    $em->persist($product);
                    $em->flush();
                }
            }
            foreach ($changesEstados as $key => $value) {
                $product = $em->getRepository(Producto::class)->find($key);
                if($product != NULL){
                    $product->setEstado($value);
                    $em->persist($product);
                    $em->flush();
                }
            }
            $response = new Response('{"success":true}');
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (\Throwable $th) {
            $response = new Response('{"success":false}');
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        
       
    }

    
}
