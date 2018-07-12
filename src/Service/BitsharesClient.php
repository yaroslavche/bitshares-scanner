<?php

namespace App\Service;

use App\Entity\Asset as AssetEntity;
use App\Entity\Market as MarketEntity;
use App\Entity\Block as BlockEntity;

use Bitshares\Bitshares;
use Bitshares\Object\ObjectFactory;

use Doctrine\Common\Persistence\ObjectManager;

class BitsharesClient
{
    /**
     * @var Bitshares
     */
    private $bitshares;

    public function __construct()
    {
        $this->bitshares = new Bitshares();
    }

    public function __get($property)
    {
        if (property_exists($this->bitshares, $property)) {
            return $this->bitshares->$property;
        }
        if (substr($property, -3) === 'Api') {
            $apiName = substr($property, 0, -3);
            if ($this->bitshares->$property) {
                return $this->bitshares->$property;
            }
            throw new \Exception(sprintf('Unknown API "%s"', $apiName));
        }
        throw new \Exception(sprintf('Unknown property "%s"', $property));
    }

    public function __call($method, $args)
    {
        if (method_exists($this->bitshares, $method)) {
            return call_user_func_array([$this->bitshares, $method], $args);
        }
    }

    public function getClient()
    {
        return $this->bitshares;
    }

    public function updateAssets(ObjectManager $manager)
    {
        $assets = $this->bitshares->getAllAssets();
        $entities = [];
        foreach ($assets as $index => $asset) {
            $assetType = AssetEntity::SMART_COINS;
            if ($asset->id === '1.3.0') {
                $assetType = AssetEntity::CORE_TOKEN;
            } elseif ($asset->issuer->id === '1.2.0') {
                $assetType = AssetEntity::USER_ISSUED_ASSETS;
            }
            $coreToken = 'BTS';
            $volume = $this->bitshares->statelessApi->get_24_volume($coreToken, $asset->symbol);
            $price = 1;
            $ticker = null;
            if ($assetType !== AssetEntity::CORE_TOKEN) {
                $ticker = $this->bitshares->statelessApi->get_ticker($coreToken, $asset->symbol);
                $price = $ticker->latest;
            }
            $currentSupply = (int)$asset->dynamic_asset_data_id->current_supply;
            $marketCap = $currentSupply * $price;
            if ($volume->base_volume > 0) {
                $marketEntity = new MarketEntity();
                $marketEntity->setPair(sprintf('%s/%s', $coreToken, $asset->symbol));
                $marketEntity->setBaseAssetId('1.3.0');
                $marketEntity->setQuoteAssetId($asset->id);
                $marketEntity->setPrice($price);
                $marketEntity->setVolume($volume->base_volume);
                $entities[] = $marketEntity;
            }
            $assetEntity = new AssetEntity();
            $assetEntity->setSymbol($asset->symbol);
            $assetEntity->setObjectId($asset->id);
            $assetEntity->setPrecision($asset->precision);
            $assetEntity->setPrice($price);
            $assetEntity->setVolume($volume->base_volume);
            $assetEntity->setMarketCap($marketCap);
            $assetEntity->setType($assetType);
            $assetEntity->setCurrentSupply($currentSupply);
            $assetEntity->setHolders(0);
            $assetEntity->setWalletType('');
            $entities[] = $assetEntity;
        }
        // truncate assets
        $cmdAsset = $manager->getClassMetadata(AssetEntity::class);
        $cmdMarket = $manager->getClassMetadata(MarketEntity::class);
        $cmdBlock = $manager->getClassMetadata(BlockEntity::class);
        $connection = $manager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($cmdAsset->getTableName());
            $connection->executeUpdate($q);
            $q = $dbPlatform->getTruncateTableSql($cmdMarket->getTableName());
            $connection->executeUpdate($q);
            // also truncate block table. No need store this data.
            $q = $dbPlatform->getTruncateTableSql($cmdBlock->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
        // TODO: refactor: yield, not store in array
        foreach ($entities as $entity) {
            $manager->persist($entity);
        }
        $manager->flush();
    }
}
