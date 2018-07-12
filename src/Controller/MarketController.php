<?php

namespace App\Controller;

use App\Entity\Market;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/market", name="market_")
 */
class MarketController extends BaseBitsharesController
{
    /**
     * @Route("/", name="index")
     */
    public function list()
    {
        $bitshares = $this->getBitshares();
        $marketRepo = $this->getDoctrine()->getRepository(Market::class);
        $markets = $marketRepo->findAll();
        // $entityManager->flush();
        return $this->render('market/list.html.twig', [
            'markets' => $markets
        ]);
    }

    /**
     * @Route("/top/{count}", name="top", requirements={"count"="\d+"})
     */
    public function getTop(int $count = 10)
    {
        $bitshares = $this->getBitshares();
        $marketRepo = $this->getDoctrine()->getRepository(Market::class);
        $markets = $marketRepo->findTop($count);
        $data = [];
        foreach ($markets as $market) {
            $data[] = [
                'pair' => $market->getPair(),
                'price' => number_format($market->getPrice(), 5)
            ];
        }
        return new Response(json_encode($data));
    }
}
