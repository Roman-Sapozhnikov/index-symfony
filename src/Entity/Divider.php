<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DividerRepository")
 */
class Divider
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $Quarter;

    /**
     * @ORM\Column(type="integer")
     */
    private $Year;

    /**
     * @ORM\Column(type="float")
     */
    private $Value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuarter(): ?int
    {
        return $this->Quarter;
    }

    public function setQuarter(int $Quarter): self
    {
        $this->Quarter = $Quarter;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->Year;
    }

    public function setYear(int $Year): self
    {
        $this->Year = $Year;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->Value;
    }

    public function setValue(float $Value): self
    {
        $this->Value = $Value;

        return $this;
    }
}
