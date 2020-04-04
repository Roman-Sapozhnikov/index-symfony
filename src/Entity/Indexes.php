<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IndexesRepository")
 */
class Indexes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $Index_Market_Cap;

    /**
     * @ORM\Column(type="float")
     */
    private $Index_8848TOP10;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIndexMarketCap(): ?float
    {
        return $this->Index_Market_Cap;
    }

    public function setIndexMarketCap(float $Index_Market_Cap): self
    {
        $this->Index_Market_Cap = $Index_Market_Cap;

        return $this;
    }

    public function getIndex8848TOP10(): ?float
    {
        return $this->Index_8848TOP10;
    }

    public function setIndex8848TOP10(float $Index_8848TOP10): self
    {
        $this->Index_8848TOP10 = $Index_8848TOP10;

        return $this;
    }
}
