<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File as File; 

/**
 * @ORM\Entity
 * @ORM\Table(name="store_inicio")
 * @Vich\Uploadable
 */
class StoreInicio
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string 
     */
    private $imagenFondo;

    /**
     * @var string
     */
    private $imagenVrx;

    /**
     * @var string
     */
    private $imagenKiwi;

    /**
     * @var string
     */
    private $fuente;
    /**
     * @var string
     */
    private $hexFuente;

    /**
     * @var string
     */
    private $hexModalBody;

    /**
     * @var string
     */
    private $hexModalHeader;

    /**
     * @var \DateTime
     */
    private $fechaActualizacion;

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
     * Set imagenFondo.
     *
     * @param string $imagenFondo
     *
     * @return StoreInicio
     */
    public function setImagenFondo($imagenFondo)
    {
        $this->imagenFondo = $imagenFondo;

        return $this;
    }

    /**
     * Get imagenFondo.
     *
     * @return string
     */
    public function getImagenFondo()
    {
        return $this->imagenFondo;
    }

    /**
     * Set imagenVrx.
     *
     * @param string $imagenVrx
     *
     * @return StoreInicio
     */
    public function setImagenVrx($imagenVrx)
    {
        $this->imagenVrx = $imagenVrx;

        return $this;
    }

    /**
     * Get imagenVrx.
     *
     * @return string
     */
    public function getImagenVrx()
    {
        return $this->imagenVrx;
    }

    /**
     * Set imagenKiwi.
     *
     * @param string $imagenKiwi
     *
     * @return StoreInicio
     */
    public function setImagenKiwi($imagenKiwi)
    {
        $this->imagenKiwi = $imagenKiwi;

        return $this;
    }

    /**
     * Get imagenKiwi.
     *
     * @return string
     */
    public function getImagenKiwi()
    {
        return $this->imagenKiwi;
    }

    /**
     * Set fuente.
     *
     * @param string $fuente
     *
     * @return StoreInicio
     */
    public function setFuente($fuente)
    {
        $this->fuente = $fuente;

        return $this;
    }

    /**
     * Get fuente.
     *
     * @return string
     */
    public function getFuente()
    {
        return $this->fuente;
    }
    /**
     * Set hexFuente.
     *
     * @param string $hexFuente
     *
     * @return StoreInicio
     */
    public function setHexFuente($hexFuente)
    {
        $this->hexFuente = $hexFuente;

        return $this;
    }

    /**
     * Get hexFuente.
     *
     * @return string
     */
    public function getHexFuente()
    {
        return $this->hexFuente;
    }

    /**
     * Set hexModalBody.
     *
     * @param string $hexModalBody
     *
     * @return StoreInicio
     */
    public function setHexModalBody($hexModalBody)
    {
        $this->hexModalBody = $hexModalBody;

        return $this;
    }

    /**
     * Get hexModalBody.
     *
     * @return string
     */
    public function getHexModalBody()
    {
        return $this->hexModalBody;
    }

    /**
     * Set hexModalHeader.
     *
     * @param string $hexModalHeader
     *
     * @return StoreInicio
     */
    public function setHexModalHeader($hexModalHeader)
    {
        $this->hexModalHeader = $hexModalHeader;

        return $this;
    }

    /**
     * Get hexModalHeader.
     *
     * @return string
     */
    public function getHexModalHeader()
    {
        return $this->hexModalHeader;
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
     * @Vich\UploadableField(mapping="store_inicio", fileNameProperty="imagen_fondo")
     *
     * @var File
     */
    private $foto1;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imagenFondo
     *
     * @return StoreInicio
     */
    public function setFoto1(File $imagenFondo = null)
    {
        $this->foto1 = $imagenFondo;

        if ($imagenFondo) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->fechaActualizacion = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFoto1()
    {
        return $this->foto1;
    }

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="store_inicio", fileNameProperty="imagen_vrx")
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
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imagenVrx
     *
     * @return StoreInicio
     */
    public function setFoto2(File $imagenVrx = null)
    {
        $this->foto2 = $imagenVrx;

        if ($imagenVrx) {
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

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="store_inicio", fileNameProperty="imagen_kiwi")
     *
     * @var File
     */
    private $foto3;
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imagenKiwi
     *
     * @return StoreInicio
     */
    public function setFoto3(File $imagenKiwi = null)
    {
        $this->foto3 = $imagenKiwi;

        if ($imagenKiwi) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->fechaActualizacion = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFoto3()
    {
        return $this->foto3;
    }
}
