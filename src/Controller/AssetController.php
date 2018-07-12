<?php

namespace App\Controller;

use App\Entity\Asset;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/asset", name="asset_")
 */
class AssetController extends BaseBitsharesController
{
    /**
     * @Route("/ws", name="ws_list")
     */
    public function index()
    {
        $assets = $this->getBitshares()->getAllAssets();
        return $this->render('asset/index.html.twig', [
            'assets' => $assets,
        ]);
    }

    /**
     * @Route("/", name="index")
     */
    public function list()
    {
        $totalVolume = $this->getDoctrine()
            ->getRepository(Asset::class)
            ->getTotalVolume();
        $assets = $this->getDoctrine()
            ->getRepository(Asset::class)
            // ->findAll();
            ->findActive();
        return $this->render('asset/list.html.twig', [
            'assets' => $assets,
            'totalVolume' => $totalVolume
        ]);
    }

    /**
     * @Route("/{symbol}", name="by_symbol")
     * @var string $symbol
     */
    public function bySymbol(string $symbol)
    {
        $assets = $this->getBitshares()->statelessApi->lookup_asset_symbols([$symbol]);
        return $this->render('asset/index.html.twig', [
            'assets' => $assets,
        ]);
    }
}
