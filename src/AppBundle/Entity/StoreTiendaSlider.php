<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File as File; 

/**
 * @ORM\Entity
 * @ORM\Table(name="store_tienda_slider")
 * @Vich\Uploadable
 */
class StoreTiendaSlider
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \AppBundle\Entity\StoreTienda
     */
    private $store; 

    /**
     * @var string
     */
    private $imagen; 

    /**
     * @var int
     */
    private $orden;

    /**
     * @var \DateTime
     */
    private $fechaModificacion;


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
     * Set imagen.
     *
     * @param string $imagen
     *
     * @return StoreTiendaSlider
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
     * Set orden.
     *
     * @param int $orden
     *
     * @return StoreTiendaSlider
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
     * Set fechaModificacion.
     *
     * @param \DateTime $fechaModificacion
     *
     * @return StoreTiendaSlider
     */
    public function setFechaModificacion($fechaModificacion)
    {
        $this->fechaModificacion = $fechaModificacion;

        return $this;
    }

    /**
     * Get fechaModificacion.
     *
     * @return \DateTime
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }
    /**
     * Set store.
     *
     * @param \AppBundle\Entity\StoreTienda|null $store
     *
     * @return StoreTiendaSlider
     */
    public function setStore(\AppBundle\Entity\StoreTienda $store = null)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Get store.
     *
     * @return \AppBundle\Entity\StoreTienda|null
     */
    public function getStore()
    {
        return $this->store;
    }
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="store_tienda_slider", fileNameProperty="imagen")
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
     * @return StoreTiendaSlider
     */
    public function setFoto(File $imagen = null)
    {
        $this->foto = $imagen;

        if ($imagen) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->fechaModificacion = new \DateTimeImmutable();
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
}
