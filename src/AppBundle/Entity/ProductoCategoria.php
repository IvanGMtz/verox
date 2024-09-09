<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File as File; 

/**
 * @ORM\Entity
 * @ORM\Table(name="producto_categoria")
 * @Vich\Uploadable
 */
class ProductoCategoria
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nombreEs;
    public function __toString() {
        return $this->nombreEs;
    }

    /**
     * @var string
     */
    private $nombreEn;
    /**
     * @var string
     */
    private $principal;
    
    /**
     * @var string
     */
    private $verox;

    /**
     * @var string
     */
    private $kiwi;

    /**
     * @var string
     */
    private $orden;

    /**
     * @var \DateTime
     */
    private $fechaActualizacion;

    /**
     * @var string
     */
    private $imagen;
    /**
     * @var string
     */
    private $imagen2;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombreEs.
     *
     * @param string $nombreEs
     *
     * @return ProductoCategoria
     */
    public function setNombreEs($nombreEs)
    {
        $this->nombreEs = $nombreEs;

        return $this;
    }

    /**
     * Get nombreEs.
     *
     * @return string
     */
    public function getNombreEs()
    {
        return $this->nombreEs;
    }

    /**
     * Set nombreEn.
     *
     * @param string $nombreEn
     *
     * @return ProductoCategoria
     */
    public function setNombreEn($nombreEn)
    {
        $this->nombreEn = $nombreEn;

        return $this;
    }

    /**
     * Get nombreEn.
     *
     * @return string
     */
    public function getNombreEn()
    {
        return $this->nombreEn;
    }
    /**
     * Set principal.
     *
     * @param string $principal
     *
     * @return ProductoCategoria
     */
    public function setPrincipal($principal)
    {
        $this->principal = $principal;

        return $this;
    }

    /**
     * Get principal.
     *
     * @return string
     */
    public function getPrincipal()
    {
        return $this->principal;
    }

    /**
     * Set verox.
     *
     * @param string $verox
     *
     * @return ProductoCategoria
     */
    public function setVerox($verox)
    {
        $this->verox = $verox;

        return $this;
    }

    /**
     * Get verox.
     *
     * @return string
     */
    public function getVerox()
    {
        return $this->verox;
    }

    /**
     * Set kiwi.
     *
     * @param string $kiwi
     *
     * @return ProductoCategoria
     */
    public function setKiwi($kiwi)
    {
        $this->kiwi = $kiwi;

        return $this;
    }

    /**
     * Get kiwi.
     *
     * @return string
     */
    public function getKiwi()
    {
        return $this->kiwi;
    }

    /**
     * Set orden.
     *
     * @param int $orden
     *
     * @return ProductoCategoria
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden.
     *
     * @return int
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set imagen.
     *
     * @param string $imagen
     *
     * @return ProductoCategoria
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen.
     *
     * @return string
     */
    public function getImagen()
    {
        return $this->imagen;
    }
    /**
     * Set imagen2.
     *
     * @param string $imagen2
     *
     * @return ProductoCategoria
     */
    public function setImagen2($imagen2)
    {
        $this->imagen2 = $imagen2;

        return $this;
    }

    /**
     * Get imagen2.
     *
     * @return string
     */
    public function getImagen2()
    {
        return $this->imagen2;
    }
    /**
     * Set fechaActualizacion.
     *
     * @param \DateTime $fechaActualizacion
     *
     * @return ProductoImagen
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Get fechaActualizacion.
     *
     * @return \DateTime
     */
    public function getFechaActualizacion()
    {
        return $this->fechaActualizacion;
    }
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="producto_categoria", fileNameProperty="imagen")
     *
     * @var File
     */
    private $foto;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imagen
     *
     * @return ProductoCategoria
     */
    public function setFoto(File $imagen = null)
    {
        $this->foto = $imagen;

        if ($imagen) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->fechaActualizacion = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFoto()
    {
        return $this->foto;
    }
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="producto_categoria", fileNameProperty="imagen2")
     *
     * @var File
     */
    private $foto2;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imagen2
     *
     * @return ProductoCategoria
     */
    public function setFoto2(File $imagen2 = null)
    {
        $this->foto2 = $imagen2;

        if ($imagen2) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->fechaActualizacion = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFoto2()
    {
        return $this->foto2;
    }
}
