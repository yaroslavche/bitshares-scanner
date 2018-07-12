<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

use Westsworld\TimeAgo;

use App\Service\BitsharesClient;

/**
 * @Route("/home", name="home_")
 */
class HomeController extends Controller
{
    /**
     * @var BitsharesClient
     */
    private $bitshares;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param BitsharesClient $bitshares
     */
    public function __construct(BitsharesClient $bitshares, TranslatorInterface $translator)
    {
        $this->bitshares = $bitshares;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $objects = $this->bitshares->databaseApi->get_objects(['2.1.0']);
        $head_block_number = $objects[0]->head_block_number;
        $start = $head_block_number;
        $end = $start - 10;
        $blocks = [];
        for ($num = $start; $num >= $end; --$num) {
            $block = $this->bitshares->databaseApi->get_block($num);
            $block->number = $num;
            $witness = $this->bitshares->databaseApi->get_objects([$block->witness]);
            $witness = $this->bitshares->databaseApi->get_objects([$witness[0]->witness_account]);
            // dump($witness[0]);
            $block->witness_name = $witness[0]->name;
            $timestamp = strtotime($block->timestamp);
            $block->date = date('d.m.Y H:i:s', $timestamp);

            /**
             * todo: allways return "Never"
             */
            $ta = new TimeAgo(null, 'en');
            $tar = $ta->inWords('d/m/Y H:i:s', $timestamp);
            $block->time_ago = $tar;

            $blocks[] = $block;
        }
        dump($blocks);
        return $this->render('home/index.html.twig', [
            'head_block_number' => $head_block_number,
            'latest_blocks' => $blocks
        ]);
    }

    /**
     * @Route("/block/{number}", name="block")
     */
    public function block($number)
    {
        $number = (int)$number;
        // get block count form get_objects in constructor and range from 1 to max
        $block = $this->bitshares->databaseApi->get_block($number);
        if (!$block) {
            throw new NotFoundHttpException('The block does not exist');
        }
        // die();
        // "transaction_merkle_root" => "36e3a83a92d30bd9d47c493f2111eca52baf771e"
        // "extensions" => []
        // "witness_signature" => "1f2a9bd6d8b052bf2c8a7ad5080bd1efc23c72767114543283da41906b73cd440863246c66657d95b0c1c8644e1b327d6559589ffaf83e12dad408f4dbdebc36d7"
        // $witness = $this->bitshares->get_object(0, [$block->witness]);
        // $witness = $this->bitshares->get_object('1.11.213277');
        // dump($witness);

        $transactions = [];
        foreach ($block->transactions as $transaction) {
            // $transactions[] = new Bitshares\Object\Transaction();
        }

        $blockData = [
            'number' => $number,
            'previous' => $block->previous,
            'date' => date('d.m.Y H:i:s', strtotime($block->timestamp)),
            'witness' => $block->witness,
            'transactions' => $transactions,
        ];
        dump($blockData);
        // foreach ($blockData['transactions'] as $index => $data) {
        //     $order = $blockData['transactions'][$index]['operations'][0][1]['order'] ?? false;
        //     $fee = $blockData['transactions'][$index]['operations'][0][1]['fee'] ?? 0;
        //     if ($order) {
        //         $result = $this->bitshares->get_objects($this->bitshares->getDatabaseApiId(), [[$order]]);
        //         $order = $result['result'][0] ?? '-';
        //         if (empty($order)) {
        //             dump($order);
        //         }
        //     }
        //     $feeAssetId = $fee['asset_id'];
        //     $result = $this->bitshares->get_assets($this->bitshares->getDatabaseApiId(), [[$feeAssetId]]);
        //     $fee['asset_name'] = $result['result'][0]['symbol'];
        //     $blockData['transactions'][$index]['order'] = $order !== false ? $order : 'NA';
        //     $blockData['transactions'][$index]['fee'] = $fee;
        // }
        // dump($blockData);
        return $this->render('home/block.html.twig', [
            'block' => $blockData,
        ]);
    }

    /**
     * @Route("/assets", name="assets")
     */
    public function assets()
    {
        $assetsIds = [];
        for ($id = 0; $id < 100; $id++) {
            $assetsIds[] = sprintf('1.3.%d', $id);
        }
        // $result = $this->bitshares->databaseApi->list_assets('AAAAA', 100);
        // dump($result);
        // die();
        $result = $this->bitshares->databaseApi->get_assets($assetsIds);
        dump($result);
        $assets = [];
        return $this->render('home/assets.html.twig', [
            'assets' => $assets,
        ]);
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        // $translated = $this->translator->trans('message');
        // dump($translated);
        $volume24 = $this->bitshares->statelessApi->get_24_volume('BTS', 'OPEN.BTC');
        dump($volume24);
        die();
        $result = $this->bitshares->header();
        die();
        return $this->render('home/block.html.twig', [
            'block' => $blockData,
        ]);
    }
}
