<?php

namespace AppBundle\Entity;

/**
 * Country
 */
class Country
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $extra1;

    /**
     * @var string
     */
    private $extra2;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $cities;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Country
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
     * Set currency
     *
     * @param string $currency
     *
     * @return Country
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set extra1
     *
     * @param string $extra1
     *
     * @return Country
     */
    public function setExtra1($extra1)
    {
        $this->extra1 = $extra1;

        return $this;
    }

    /**
     * Get extra1
     *
     * @return string
     */
    public function getExtra1()
    {
        return $this->extra1;
    }

    /**
     * Set extra2
     *
     * @param string $extra2
     *
     * @return Country
     */
    public function setExtra2($extra2)
    {
        $this->extra2 = $extra2;

        return $this;
    }

    /**
     * Get extra2
     *
     * @return string
     */
    public function getExtra2()
    {
        return $this->extra2;
    }

    /**
     * Add city
     *
     * @param \AppBundle\Entity\City $city
     *
     * @return Country
     */
    public function addCity(\AppBundle\Entity\City $city)
    {
        $this->cities[] = $city;

        return $this;
    }

    /**
     * Remove city
     *
     * @param \AppBundle\Entity\City $city
     */
    public function removeCity(\AppBundle\Entity\City $city)
    {
        $this->cities->removeElement($city);
    }

    /**
     * Get cities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCities()
    {
        return $this->cities;
    }
    
    public function __toString() {
      return $this->name;
    }
}
