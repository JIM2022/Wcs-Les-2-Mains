<?php

namespace les2MainsBundle\Entity;

/**
 * Events
 */
class Events
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $datesDebut;

    /**
     * @var \DateTime
     */
    private $datesFin;

    /**
     * @var int
     */
    private $prix;

    /**
     * @var string
     */
    private $descriptions;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set datesDebut
     *
     * @param \DateTime $datesDebut
     *
     * @return Events
     */
    public function setDatesDebut($datesDebut)
    {
        $this->datesDebut = $datesDebut;

        return $this;
    }

    /**
     * Get datesDebut
     *
     * @return \DateTime
     */
    public function getDatesDebut()
    {
        return $this->datesDebut;
    }

    /**
     * Set datesFin
     *
     * @param \DateTime $datesFin
     *
     * @return Events
     */
    public function setDatesFin($datesFin)
    {
        $this->datesFin = $datesFin;

        return $this;
    }

    /**
     * Get datesFin
     *
     * @return \DateTime
     */
    public function getDatesFin()
    {
        return $this->datesFin;
    }

    /**
     * Set prix
     *
     * @param integer $prix
     *
     * @return Events
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return int
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set descriptions
     *
     * @param string $descriptions
     *
     * @return Events
     */
    public function setDescriptions($descriptions)
    {
        $this->descriptions = $descriptions;

        return $this;
    }

    /**
     * Get descriptions
     *
     * @return string
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }
}

