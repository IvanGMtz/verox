<?php

namespace AppBundle\Entity;

/**
 * StoreSeo
 */
class StoreSeo
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $facebookPixel;

    /**
     * @var string
     */
    private $googleAnalytics;

    /**
     * @var string
     */
    private $keyWords;


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
     * Set facebookPixel.
     *
     * @param string $facebookPixel
     *
     * @return StoreSeo
     */
    public function setFacebookPixel($facebookPixel)
    {
        $this->facebookPixel = $facebookPixel;

        return $this;
    }

    /**
     * Get facebookPixel.
     *
     * @return string
     */
    public function getFacebookPixel()
    {
        return $this->facebookPixel;
    }

    /**
     * Set googleAnalytics.
     *
     * @param string $googleAnalytics
     *
     * @return StoreSeo
     */
    public function setGoogleAnalytics($googleAnalytics)
    {
        $this->googleAnalytics = $googleAnalytics;

        return $this;
    }

    /**
     * Get googleAnalytics.
     *
     * @return string
     */
    public function getGoogleAnalytics()
    {
        return $this->googleAnalytics;
    }

    /**
     * Set keyWords.
     *
     * @param string $keyWords
     *
     * @return StoreSeo
     */
    public function setKeyWords($keyWords)
    {
        $this->keyWords = $keyWords;

        return $this;
    }

    /**
     * Get keyWords.
     *
     * @return string
     */
    public function getKeyWords()
    {
        return $this->keyWords;
    }
}
