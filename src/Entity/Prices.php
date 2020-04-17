<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PricesRepository")
 */
class Prices
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
    private $cur_id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $open;

    /**
     * @ORM\Column(type="float")
     */
    private $high;

    /**
     * @ORM\Column(type="float")
     */
    private $low;

    /**
     * @ORM\Column(type="float")
     */
    private $close;

    /**
     * @ORM\Column(type="float")
     */
    private $volume;

    /**
     * @ORM\Column(type="float")
     */
    private $market_cap;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurId(): ?int
    {
        return $this->cur_id;
    }

    public function setCurId(int $cur_id): self
    {
        $this->cur_id = $cur_id;

        return $this;
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

    public function getOpen(): ?float
    {
        return $this->open;
    }

    public function setOpen(float $open): self
    {
        $this->open = $open;

        return $this;
    }

    public function getHigh(): ?float
    {
        return $this->high;
    }

    public function setHigh(float $high): self
    {
        $this->high = $high;

        return $this;
    }

    public function getLow(): ?float
    {
        return $this->low;
    }

    public function setLow(float $low): self
    {
        $this->low = $low;

        return $this;
    }

    public function getClose(): ?float
    {
        return $this->close;
    }

    public function setClose(float $close): self
    {
        $this->close = $close;

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getMarketCap(): ?float
    {
        return $this->market_cap;
    }

    public function setMarketCap(float $market_cap): self
    {
        $this->market_cap = $market_cap;

        return $this;
    }

}
