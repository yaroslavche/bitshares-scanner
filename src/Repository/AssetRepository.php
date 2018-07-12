<?php

namespace App\Repository;

use App\Entity\Asset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Asset|null find($id, $lockMode = null, $lockVersion = null)
 * @method Asset|null findOneBy(array $criteria, array $orderBy = null)
 * @method Asset[]    findAll()
 * @method Asset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Asset::class);
    }

    /**
     * @return int $volume
     */
    public function getTotalVolume()
    {
        $result = $this->createQueryBuilder('asset')
            ->andWhere('asset.objectId != :objectId')
            ->setParameter('objectId', '1.3.0')
            ->select('SUM(asset.volume) as totalVolume')
            ->orderBy('asset.symbol', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return (int)$result[0]['totalVolume'];
    }

    /**
     * @return Asset[] $assets
     */
    public function findActive()
    {
        $assets = $this->createQueryBuilder('asset')
            ->andWhere('asset.volume > :volume')
            ->setParameter('volume', 0)
            ->orderBy('asset.price', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $assets;
    }

    /**
     * @param string $symbol
     * @return ?Asset
     */
    public function findBySymbol(string $symbol): ?Asset
    {
        return $this->createQueryBuilder('asset')
            ->andWhere('asset.symbol = :symbol')
            ->setParameter('symbol', $symbol)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
