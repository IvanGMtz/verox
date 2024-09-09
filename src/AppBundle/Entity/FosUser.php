<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Avanzu\AdminThemeBundle\Model\UserInterface as ThemeUser;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File as File;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @Vich\Uploadable
 */
class FosUser extends BaseUser implements ThemeUser
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
     protected $id;

     public function getIdentifier() {
       return $this->id;
     }

     public function getMemberSince() {

     }

     public function getTitle() {
     }

     public function isOnline() {
     }

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $docType;

    /**
     * @var string
     */
    private $doc;

    /**
     * @var \DateTime
     */
    private $birthday;

    /**
     * @var string
     */
    private $serial;

    /**
     * @var boolean
     */
    private $changePassword = '1';

    /**
     * @var \AppBundle\Entity\City
     */
    private $city;

    /**
     * @var \AppBundle\Entity\Country
     */
    private $country;
    
    /**
     * @var \DateTime|null
     */
    private $updatedAt;
    
    /**
     * @var string|null
     */
    private $image;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return FosUser
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return FosUser
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return FosUser
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set docType
     *
     * @param string $docType
     *
     * @return FosUser
     */
    public function setDocType($docType)
    {
        $this->docType = $docType;

        return $this;
    }

    /**
     * Get docType
     *
     * @return string
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     * Set doc
     *
     * @param string $doc
     *
     * @return FosUser
     */
    public function setDoc($doc)
    {
        $this->doc = $doc;

        return $this;
    }

    /**
     * Get doc
     *
     * @return string
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return FosUser
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set serial
     *
     * @param string $serial
     *
     * @return FosUser
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial
     *
     * @return string
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Set changePassword
     *
     * @param boolean $changePassword
     *
     * @return FosUser
     */
    public function setChangePassword($changePassword)
    {
        $this->changePassword = $changePassword;

        return $this;
    }

    /**
     * Get changePassword
     *
     * @return boolean
     */
    public function getChangePassword()
    {
        return $this->changePassword;
    }

    /**
     * Set city
     *
     * @param \AppBundle\Entity\City $city
     *
     * @return FosUser
     */
    public function setCity(\AppBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \AppBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param \AppBundle\Entity\Country $country
     *
     * @return FosUser
     */
    public function setCountry(\AppBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \AppBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set image.
     *
     * @param string|null $image
     *
     * @return FosUser
     */
    public function setImage($image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string|null
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * @var \DateTime|null
     */
    private $registeredAt;


    /**
     * Set registeredAt.
     *
     * @param \DateTime|null $registeredAt
     *
     * @return FosUser
     */
    public function setRegisteredAt($registeredAt = null)
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    /**
     * Get registeredAt.
     *
     * @return \DateTime|null
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime|null $updatedAt
     *
     * @return FosUser
     */
    public function setUpdatedAt($updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="fos_user_image", fileNameProperty="image")
     * 
     * @var File
     */
    private $avatar;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return FosUser
     */
    public function setAvatar(File $image = null)
    {
        $this->avatar = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
    
}
