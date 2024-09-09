<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File as File; 

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="store_tienda")
 * @Vich\Uploadable
 */
class StoreTienda
{
    /**
     * @var int
     */
    private $id; 
    
    /**
     * @var string
     */
    private $nombre;
    /**
     * @var string
     */
    private $fuenteNavbar;

    /**
     * @var string
     */
    private $hexFuenteNavbar;

    /**
     * @var string
     */
    private $hexFondoNavbar;

    /**
     * @var string
     */
    private $whatsappMainColor;

    /**
     * @var string
     */
    private $whatsappTextColor;

    /**
     * @var string
     */
    private $logoNavbar;
    /**
     * @var string
     */
    private $mayoristasImagen;

    /**
     * @var string
     */
    private $mayoristasImagen2;

    /**
     * @var string
     */
    private $mayoristasImagen3;

    /**
     * @var string
     */
    private $mayoristasImagen4;

    /**
     * @var string
     */
    private $mayoristasImagen5;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $imagenesSlider;

    /**
     * @var \DateTime
     */
    private $fechaModificacion;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->imagenesSlider = new ArrayCollection();
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
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return StoreTienda
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }
    /**
     * Set fuenteNavbar.
     *
     * @param string $fuenteNavbar
     *
     * @return StoreTienda
     */
    public function setFuenteNavbar($fuenteNavbar)
    {
        $this->fuenteNavbar = $fuenteNavbar;

        return $this;
    }

    /**
     * Get fuenteNavbar.
     *
     * @return string
     */
    public function getFuenteNavbar()
    {
        return $this->fuenteNavbar;
    }

    /**
     * Set hexFuenteNavbar.
     *
     * @param string $hexFuenteNavbar
     *
     * @return StoreTienda
     */
    public function setHexFuenteNavbar($hexFuenteNavbar)
    {
        $this->hexFuenteNavbar = $hexFuenteNavbar;

        return $this;
    }

    /**
     * Get hexFuenteNavbar.
     *
     * @return string
     */
    public function getHexFuenteNavbar()
    {
        return $this->hexFuenteNavbar;
    }

    /**
     * Set hexFondoNavbar.
     *
     * @param string $hexFondoNavbar
     *
     * @return StoreTienda
     */
    public function setHexFondoNavbar($hexFondoNavbar)
    {
        $this->hexFondoNavbar = $hexFondoNavbar;

        return $this;
    }

    /**
     * Get hexFondoNavbar.
     *
     * @return string
     */
    public function getHexFondoNavbar()
    {
        return $this->hexFondoNavbar;
    }

    /**
     * Set whatsappMainColor.
     *
     * @param string $whatsappMainColor
     *
     * @return StoreTienda
     */
    public function setWhatsappMainColor($whatsappMainColor)
    {
        $this->whatsappMainColor = $whatsappMainColor;

        return $this;
    }

    /**
     * Get whatsappMainColor.
     *
     * @return string
     */
    public function getWhatsappMainColor()
    {
        return $this->whatsappMainColor;
    }

    /**
     * Set whatsappTextColor.
     *
     * @param string $whatsappTextColor
     *
     * @return StoreTienda
     */
    public function setWhatsappTextColor($whatsappTextColor)
    {
        $this->whatsappTextColor = $whatsappTextColor;

        return $this;
    }

    /**
     * Get whatsappTextColor.
     *
     * @return string
     */
    public function getWhatsappTextColor()
    {
        return $this->whatsappTextColor;
    }

    /**
     * Set logoNavbar.
     *
     * @param string $logoNavbar
     *
     * @return StoreTienda
     */
    public function setLogoNavbar($logoNavbar)
    {
        $this->logoNavbar = $logoNavbar;

        return $this;
    }

    /**
     * Get logoNavbar.
     *
     * @return string
     */
    public function getLogoNavbar()
    {
        return $this->logoNavbar;
    }

    /**
     * Set mayoristasImagen.
     *
     * @param string $mayoristasImagen
     *
     * @return StoreTienda
     */
    public function setMayoristasImagen($mayoristasImagen)
    {
        $this->mayoristasImagen = $mayoristasImagen;

        return $this;
    }

    /**
     * Get mayoristasImagen.
     *
     * @return string
     */
    public function getMayoristasImagen()
    {
        return $this->mayoristasImagen;
    }

    /**
     * Set mayoristasImagen2.
     *
     * @param string $mayoristasImagen2
     *
     * @return StoreTienda
     */
    public function setMayoristasImagen2($mayoristasImagen2)
    {
        $this->mayoristasImagen2 = $mayoristasImagen2;

        return $this;
    }

    /**
     * Get mayoristasImagen2.
     *
     * @return string
     */
    public function getMayoristasImagen2()
    {
        return $this->mayoristasImagen2;
    }

    /**
     * Set mayoristasImagen3.
     *
     * @param string $mayoristasImagen3
     *
     * @return StoreTienda
     */
    public function setMayoristasImagen3($mayoristasImagen3)
    {
        $this->mayoristasImagen3 = $mayoristasImagen3;

        return $this;
    }

    /**
     * Get mayoristasImagen3.
     *
     * @return string
     */
    public function getMayoristasImagen3()
    {
        return $this->mayoristasImagen3;
    }

    /**
     * Set mayoristasImagen4.
     *
     * @param string $mayoristasImagen4
     *
     * @return StoreTienda
     */
    public function setMayoristasImagen4($mayoristasImagen4)
    {
        $this->mayoristasImagen4 = $mayoristasImagen4;

        return $this;
    }

    /**
     * Get mayoristasImagen4.
     *
     * @return string
     */
    public function getMayoristasImagen4()
    {
        return $this->mayoristasImagen4;
    }

    /**
     * Set mayoristasImagen5.
     *
     * @param string $mayoristasImagen5
     *
     * @return StoreTienda
     */
    public function setMayoristasImagen5($mayoristasImagen5)
    {
        $this->mayoristasImagen5 = $mayoristasImagen5;

        return $this;
    }

    /**
     * Get mayoristasImagen5.
     *
     * @return string
     */
    public function getMayoristasImagen5()
    {
        return $this->mayoristasImagen5;
    }

    /**
     * Set fechaModificacion.
     *
     * @param \DateTime $fechaModificacion
     *
     * @return StoreTienda
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
     * Add imagenesSlider.
     *
     * @param \AppBundle\Entity\StoreTiendaSlider $imagenesSlider
     *
     * @return StoreTienda
     */
    public function addImagenesSlider(\AppBundle\Entity\StoreTiendaSlider $imagenesSlider)
    {
        if (!$this->imagenesSlider->contains($imagenesSlider)) {
          $imagenesSlider->setStore($this);
          $this->imagenesSlider[] = $imagenesSlider;
        }
        return $this;
    }

    /**
     * Remove imagenesSlider.
     *
     * @param \AppBundle\Entity\StoreTiendaSlider $imagenesSlider
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeImagenesSlider(\AppBundle\Entity\StoreTiendaSlider $imagenesSlider)
    {
        //dump($this->imagenesSlider->removeElement($imagenesSlider));exit;
        return $this->imagenesSlider->removeElement($imagenesSlider);
    }

    /**
     * Get imagenesSlider.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImagenesSlider()
    {
        return $this->imagenesSlider;
    }

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="store_tienda", fileNameProperty="logo_navbar")
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
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $logoNavbar
     *
     * @return StoreTienda
     */
    public function setFoto(File $logoNavbar = null)
    {
        $this->foto = $logoNavbar;

        if ($logoNavbar) {
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

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="store_tienda", fileNameProperty="mayoristas_imagen")
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
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $mayoristasImagen
     *
     * @return StoreTienda
     */
    public function setFoto2(File $mayoristasImagen = null)
    {
        $this->foto2 = $mayoristasImagen;

        if ($mayoristasImagen) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->fechaModificacion = new \DateTimeImmutable();
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
     * @Vich\UploadableField(mapping="store_tienda", fileNameProperty="mayoristas_imagen2")
     *
     * @var File
     */
    private $foto3;
    /**
    * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $mayoristasImagen2
    * @return StoreTienda
    */
    public function setFoto3(File $mayoristasImagen2 = null)
    {
        $this->foto3 = $mayoristasImagen2;

        if ($mayoristasImagen2) {
            $this->fechaModificacion = new \DateTimeImmutable();
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

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="store_tienda", fileNameProperty="mayoristas_imagen3")
     *
     * @var File
     */
    private $foto4;
    /**
    * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $mayoristasImagen3
    * @return StoreTienda
    */
    public function setFoto4(File $mayoristasImagen3 = null)
    {
        $this->foto4 = $mayoristasImagen3;

        if ($mayoristasImagen3) {
            $this->fechaModificacion = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFoto4()
    {
        return $this->foto4;
    }

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="store_tienda", fileNameProperty="mayoristas_imagen4")
     *
     * @var File
     */
    private $foto5;
    /**
    * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $mayoristasImagen4
    * @return StoreTienda
    */
    public function setFoto5(File $mayoristasImagen4 = null)
    {
        $this->foto5 = $mayoristasImagen4;

        if ($mayoristasImagen4) {
            $this->fechaModificacion = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFoto5()
    {
        return $this->foto5;
    }

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="store_tienda", fileNameProperty="mayoristas_imagen5")
     *
     * @var File
     */
    private $foto6;
    /**
    * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $mayoristasImagen5
    * @return StoreTienda
    */
    public function setFoto6(File $mayoristasImagen5 = null)
    {
        $this->foto6 = $mayoristasImagen5;

        if ($mayoristasImagen5) {
            $this->fechaModificacion = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFoto6()
    {
        return $this->foto6;
    }
}
