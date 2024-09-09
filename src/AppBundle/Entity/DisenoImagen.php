<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File as File; 

/**
 * @ORM\Entity
 * @ORM\Table(name="diseno_imagen")
 * @Vich\Uploadable
 */ 
class DisenoImagen
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

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
    private $fechaActualizacion;

    /**
     * @var int
     */
    private $estado = 1;

    /**
     * @var \AppBundle\Entity\Diseno
     */
    private $diseno;


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
     * @return DisenoImagen
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
     * @return DisenoImagen
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
     * Set fechaActualizacion.
     *
     * @param \DateTime $fechaActualizacion
     *
     * @return DisenoImagen
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
     * Set estado.
     *
     * @param int $estado
     *
     * @return DisenoImagen
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado.
     *
     * @return int
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set diseno.
     *
     * @param \AppBundle\Entity\Diseno|null $diseno
     *
     * @return DisenoImagen
     */
    public function setDiseno(\AppBundle\Entity\Diseno $diseno = null)
    {
        $this->diseno = $diseno;

        return $this;
    }

    /**
     * Get diseno.
     *
     * @return \AppBundle\Entity\Diseno|null
     */
    public function getDiseno()
    {
        return $this->diseno;
    }

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="diseno_imagen", fileNameProperty="imagen")
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
     * @return DisenoImagen
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
}
