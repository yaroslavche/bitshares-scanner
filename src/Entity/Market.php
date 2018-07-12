<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TODO: remove supply
 *
 * @ORM\Entity(repositoryClass="App\Repository\MarketRepository")
 */
class Market
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pair;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $baseAssetId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $quoteAssetId;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="bigint")
     */
    private $volume;

    public function getId()
    {
        return $this->id;
    }

    public function getPair(): ?string
    {
        return $this->pair;
    }

    public function setPair(string $pair): self
    {
        $this->pair = $pair;

        return $this;
    }

    public function getBaseAssetId(): ?string
    {
        return $this->baseAssetId;
    }

    public function setBaseAssetId(string $baseAssetId): self
    {
        $this->baseAssetId = $baseAssetId;

        return $this;
    }

    public function getQuoteAssetId(): ?string
    {
        return $this->quoteAssetId;
    }

    public function setQuoteAssetId(string $quoteAssetId): self
    {
        $this->quoteAssetId = $quoteAssetId;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function setVolume(int $volume): self
    {
        $this->volume = $volume;

        return $this;
    }
}
