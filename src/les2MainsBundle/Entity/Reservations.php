<?php

namespace les2MainsBundle\Entity;

/**
 * Reservations
 */
class Reservations
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $quantitéInitiale;

    /**
     * @var int
     */
    private $quantitéReservé;

    /**
     * @var int
     */
    private $prix;

    /**
     * @var \DateTime
     */
    private $dateReservation;

    /**
     * @var \DateTime
     */
    private $dateVente;


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
     * Set quantitéInitiale
     *
     * @param integer $quantitéInitiale
     *
     * @return Reservations
     */
    public function setQuantitéInitiale($quantitéInitiale)
    {
        $this->quantitéInitiale = $quantitéInitiale;

        return $this;
    }

    /**
     * Get quantitéInitiale
     *
     * @return int
     */
    public function getQuantitéInitiale()
    {
        return $this->quantitéInitiale;
    }

    /**
     * Set quantitéReservé
     *
     * @param integer $quantitéReservé
     *
     * @return Reservations
     */
    public function setQuantitéReservé($quantitéReservé)
    {
        $this->quantitéReservé = $quantitéReservé;

        return $this;
    }

    /**
     * Get quantitéReservé
     *
     * @return int
     */
    public function getQuantitéReservé()
    {
        return $this->quantitéReservé;
    }

    /**
     * Set prix
     *
     * @param integer $prix
     *
     * @return Reservations
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
     * Set dateReservation
     *
     * @param \DateTime $dateReservation
     *
     * @return Reservations
     */
    public function setDateReservation($dateReservation)
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    /**
     * Get dateReservation
     *
     * @return \DateTime
     */
    public function getDateReservation()
    {
        return $this->dateReservation;
    }

    /**
     * Set dateVente
     *
     * @param \DateTime $dateVente
     *
     * @return Reservations
     */
    public function setDateVente($dateVente)
    {
        $this->dateVente = $dateVente;

        return $this;
    }

    /**
     * Get dateVente
     *
     * @return \DateTime
     */
    public function getDateVente()
    {
        return $this->dateVente;
    }
}
