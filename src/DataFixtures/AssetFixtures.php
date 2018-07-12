<?php

namespace App\DataFixtures;

use App\Entity\Asset as AssetEntity;
use App\Entity\Market as MarketEntity;
use Bitshares\Bitshares;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AssetFixtures extends Fixture
{
    /**
     * 3626 processed 436.25s. ~7.5min
     *
     * @param  ObjectManager $manager [description]
     * @return [type]                 [description]
     */
    public function load(ObjectManager $manager)
    {
        $starttime = microtime(true);
        dump('Get assets...');
        $bitshares = new Bitshares();
        $assets = $bitshares->getAllAssets();
        $total = count($assets);
        dump(sprintf('Total assets: %d', $total));
        $entities = [];
        foreach ($assets as $index => $asset) {
            // if ($index > 100) {
            //     break;
            // }
            // TODO: move type constants to Bitshares\Object\Protocol\Asset
            $assetType = AssetEntity::SMART_COINS;
            if ($asset->id === '1.3.0') {
                $assetType = AssetEntity::CORE_TOKEN;
            } elseif ($asset->issuer->id === '1.2.0') {
                $assetType = AssetEntity::USER_ISSUED_ASSETS;
            }
            // TODO: 'BTS' => onload get_asset('1.3.0') and save to global variable
            // $coreToken = $bitshares->getCoreToken()->getSymbol();
            $coreToken = 'BTS';
            $volume = $bitshares->statelessApi->get_24_volume($coreToken, $asset->symbol);
            $price = 1;
            $ticker = null;
            if ($assetType !== AssetEntity::CORE_TOKEN) {
                $ticker = $bitshares->statelessApi->get_ticker($coreToken, $asset->symbol);
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
            $percent = (($index + 1) / $total) * 100;
            $endtime = microtime(true);
            $timediff = $endtime - $starttime;
            dump(sprintf('%d processed (%.2f%%) %.2fs', $index + 1, $percent, $timediff));
        }
        foreach ($entities as $entity) {
            $manager->persist($entity);
        }
        dump(sprintf('flush (100%%)'));
        $manager->flush();
    }
}
