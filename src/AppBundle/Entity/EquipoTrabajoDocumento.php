<?php

namespace AppBundle\Entity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File as File;

/**
 * EquipoTrabajoDocumento
 */
class EquipoTrabajoDocumento
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $documento;

    /**
     * @var \DateTime
     */
    private $fechaActualizacion;

    /**
     * @var \AppBundle\Entity\DocumentoEtiqueta
     */
    private $etiqueta;

    /**
     * @var \AppBundle\Entity\EquipoTrabajo
     */
    private $trabajador;

    /**
     * @Vich\UploadableField(mapping="equipo_trabajo_documento", fileNameProperty="document")
     * @var File
     */
    private $document;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $documento
     *
     * @return EquipoTrabajoDocumento
     */

     public function setDocument(File $documento = null)
     {
         $this->document = $documento;

         if ($documento) {
             // It is required that at least one field changes if you are using doctrine
             // otherwise the event listeners won't be called and the file is lost
             $this->fechaActualizacion = new \DateTimeImmutable();
         }

         return $this;
     }

     /**
      * @return File|null
      */
     public function getDocument()
     {
         return $this->document;
     }

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
     * Set documento.
     *
     * @param string $documento
     *
     * @return EquipoTrabajoDocumento
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;

        return $this;
    }

    /**
     * Get documento.
     *
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * Set fechaActualizacion.
     *
     * @param \DateTime $fechaActualizacion
     *
     * @return EquipoTrabajoDocumento
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
     * Set etiqueta.
     *
     * @param \AppBundle\Entity\DocumentoEtiqueta|null $etiqueta
     *
     * @return EquipoTrabajoDocumento
     */
    public function setEtiqueta(\AppBundle\Entity\DocumentoEtiqueta $etiqueta = null)
    {
        $this->etiqueta = $etiqueta;

        return $this;
    }

    /**
     * Get etiqueta.
     *
     * @return \AppBundle\Entity\DocumentoEtiqueta|null
     */
    public function getEtiqueta()
    {
        return $this->etiqueta;
    }

    /**
     * Set trabajador.
     *
     * @param \AppBundle\Entity\EquipoTrabajo|null $trabajador
     *
     * @return EquipoTrabajoDocumento
     */
    public function setTrabajador(\AppBundle\Entity\EquipoTrabajo $trabajador = null)
    {
        $this->trabajador = $trabajador;

        return $this;
    }

    /**
     * Get trabajador.
     *
     * @return \AppBundle\Entity\EquipoTrabajo|null
     */
    public function getTrabajador()
    {
        return $this->trabajador;
    }
}
