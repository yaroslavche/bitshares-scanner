<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetRepository")
 * @see https://github.com/oxarbitrage/bitshares-python-api-backend/blob/master/postgres/import_assets.py ported from there
 */
class Asset
{
    /**
     * Core Token type
     * @var int
     */
    const CORE_TOKEN = 1;

    /**
     * User Issued Asset type
     * @var int
     */
    const USER_ISSUED_ASSETS = 2;

    /**
     * Smart Coin type
     * @var int
     */
    const SMART_COINS = 3;

    /**
     * Internal id of asset
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int $id
     */
    private $id;

    /**
     * Asset symbol
     * @example BTS
     *
     * @ORM\Column(name="`symbol`", length=20)
     * @var string $symbol
     */
    private $symbol;

    /**
     * Object id in bitshares ("space.type.id")
     * @example 1.2.567 - Protocol space "1", Account object type "2", id = "567"
     *
     * @ORM\Column(name="`object_id`", length=20)
     * @var string $objectId
     */
    private $objectId;

    /**
     * Precission
     *
     * @ORM\Column(name="`precision`", type="integer", nullable=true)
     * @var int $precision
     */
    private $precision;

    /**
     * Price in BTS (Core Token)
     * !!! Doctrine type decimal return string, cast to float in getter
     *
     * @ORM\Column(name="`price`", type="decimal", nullable=true)
     * @var float $price
     */
    private $price;

    /**
     * !!! Doctrine type decimal return string, cast to float in getter
     *
     * @ORM\Column(name="`volume`", type="decimal", nullable=true)
     * @var float $volume
     */
    private $volume;

    /**
     * Market cap
     * !!! decimal out of bounds. Used bigint instead
     *
     * @ORM\Column(name="`market_cap`", type="bigint", nullable=true)
     * @var int $marketCap
     */
    private $marketCap;

    /**
     * Asset type
     * @example static::CORE_TOKEN, static::USER_ISSUED_ASSETS and static::SMART_COINS
     *
     * @ORM\Column(name="`type`", type="smallint")
     * @var int $type
     */
    private $type;

    /**
     * Current supply for asset
     *
     * @ORM\Column(name="`current_supply`", type="bigint", nullable=true)
     * @var int $currentSupply
     */
    private $currentSupply;

    /**
     * Holders count
     *
     * @ORM\Column(name="`holders_count`", type="integer", nullable=true)
     * @var int $holders
     */
    private $holders;

    /**
     * @ORM\Column(name="`wallet_type`", length=40, nullable=true)
     * @var string $walletType
     */
    private $walletType;

    /**
     *
     */
    public function __construct()
    {
        $this->precision = 5;
    }

    /**
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string $symbol
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     * @return self
     */
    public function setSymbol(string $symbol)
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @return string $objectId
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param string $objectId
     * @return self
     */
    public function setObjectId(string $objectId)
    {
        $this->objectId = $objectId;
        return $this;
    }

    /**
     * @return float $precision
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @param int $precision
     * @return self
     */
    public function setPrecision(int $precision)
    {
        $this->precision = $precision;
        return $this;
    }

    /**
     * @return float $price
     */
    public function getPrice()
    {
        return (float)$this->price;
    }

    /**
     * @param float $price
     * @return self
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return float $volume
     */
    public function getVolume()
    {
        return (float)$this->volume;
    }

    /**
     * @param float $volume volume
     * @return self
     */
    public function setVolume(float $volume)
    {
        $this->volume = $volume;
        return $this;
    }

    /**
     * @return int $marketCap
     */
    public function getMarketCap()
    {
        return $this->marketCap;
    }

    /**
     * @param int $marketCap
     * @return self
     */
    public function setMarketCap(int $marketCap)
    {
        $this->marketCap = $marketCap;
        return $this;
    }

    /**
     * @return int $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return self
     */
    public function setType(int $type)
    {
        if (!in_array($type, [static::CORE_TOKEN, static::USER_ISSUED_ASSETS, static::SMART_COINS])) {
            throw new \Exception(sprintf('Invalid asset type "%d"', $type));
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @return int $currentSupply
     */
    public function getCurrentSupply()
    {
        return $this->currentSupply;
    }

    /**
     * @param int $currentSupply
     * @return self
     */
    public function setCurrentSupply(int $currentSupply)
    {
        $this->currentSupply = $currentSupply;
        return $this;
    }

    /**
     * @return int $holder
     */
    public function getHolders()
    {
        return $this->holders;
    }

    /**
     * @param int $holders
     * @return self
     */
    public function setHolders(int $holders)
    {
        $this->holders = $holders;
        return $this;
    }

    /**
     * @return string $walletType
     */
    public function getWalletType()
    {
        return $this->walletType;
    }

    /**
     * @param string $walletType walletType
     * @return self
     */
    public function setWalletType(string $walletType)
    {
        $this->walletType = $walletType;
        return $this;
    }
}
