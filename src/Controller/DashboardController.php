<?php

namespace App\Controller;

use App\Entity\Block;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;

use Bitshares\Object\Protocol\OperationHelper;

/**
 * @Route("/", name="dashboard_")
 */
class DashboardController extends BaseBitsharesController
{
    /**
     * count of latest blocks
     * @var int
     */
    const BLOCK_COUNT = 20;

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $latestBlock = $this->bitshares->dynamicGlobalProperties->head_block_number;
        // $this->getLatest($latestBlock);
        $blockRepo = $this->getDoctrine()->getRepository(Block::class);
        // $latestBlocks = $blockRepo->findBy([], ['id' => 'DESC'], static::BLOCK_COUNT);
        $witnessCount = $this->bitshares->statelessApi->get_witness_count();
        $committeeCount = $this->bitshares->statelessApi->get_committee_count();
        $object = $this->bitshares->getObject('2.3.0');
        $currentSupply = (int)$object->current_supply;
        $confidentalSupply = (int)$object->confidential_supply;
        $marketCap = $currentSupply + $confidentalSupply;
        $BTSMarketCap = $marketCap / 100000000;
        return $this->render('dashboard/index.html.twig', [
            'ws' => $this->bitshares->getConnectedServer(),
            'latestBlocks' => [],
            'dynamicGlobalProperties' => $this->bitshares->dynamicGlobalProperties,
            'globalProperties' => $this->bitshares->globalProperties,
            'witnessCount' => $witnessCount,
            'committeeCount' => $committeeCount,
            'BTSMarketCap' => $BTSMarketCap,
            'currentSupply' => $currentSupply / 100000,
            'confidentalSupply' => $confidentalSupply / 100000
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request)
    {
        $searchQuery = addslashes(htmlspecialchars($request->request->get('search')));
        $block = null;
        $assets = null;
        $accounts = null;
        if (intval($searchQuery) > 0) {
            $block = $this->bitshares->statelessApi->get_block(intval($searchQuery));
            if ($block) {
                $block = intval($searchQuery);
            }
        }
        $accounts = $this->bitshares->statelessApi->lookup_account_names([$searchQuery]);
        if (!isset($accounts[0]) || is_null($accounts[0])) {
            $accounts = null;
        }
        try {
            $assets = $this->bitshares->statelessApi->lookup_asset_symbols([$searchQuery]);
            if (!isset($assets[0]) || is_null($assets[0])) {
                $assets = null;
            }
        } catch (\Exception $e) {
        }
        if (is_null($accounts) && is_null($assets) && !is_null($block)) {
            return $this->redirectToRoute('block_by_number', ['number' => $block]);
        }
        if (!is_null($accounts) && is_null($assets) && is_null($block)) {
            return $this->redirectToRoute('account_by_name', ['name' => $accounts[0]->name]);
        }
        return $this->render('dashboard/search.html.twig', [
            'query' => $searchQuery,
            'block' => $block,
            'assets' => $assets,
            'accounts' => $accounts
        ]);
    }

    /**
     * get latest blocks
     * @todo truncate table sometimes
     * @param int $latestBlock number of latest block
     * @return
     */
    private function getLatest($latestBlock)
    {
        $blockRepo = $this->getDoctrine()->getRepository(Block::class);
        $latestInDB = $blockRepo->findBy([], ['number' => 'DESC'], static::BLOCK_COUNT);
        $latestBlockInDB = !empty($latestInDB) ? $latestInDB[0]->getNumber() : $latestBlock - 10;
        if ($latestBlockInDB < $latestBlock) {
            $entityManager = $this->getDoctrine()->getManager();
            for ($number = $latestBlock - 10; $number <= $latestBlock; $number++) {
                foreach ($latestInDB as $block) {
                    if ($block->getNumber() === (int)$number) {
                        continue 2;
                    }
                }
                $blockData = $this->bitshares->statelessApi->get_block($number);
                $block = new Block();
                $block->setNumber($number);
                $block->setPrevious($blockData->previous);
                $block->setTimestamp(new \DateTime($blockData->timestamp));
                $block->setWitness($blockData->witness);
                $block->setTransactionMerkleRoot($blockData->transaction_merkle_root);
                $block->setTransactionsCount(count($blockData->transactions));
                // $block->setTransactions($blockData->transactions);
                $entityManager->persist($block);
            }
            $entityManager->flush();
        }
    }

    /**
     * @Route("/subscribe", name="subscribe")
     */
    public function subscribe()
    {
        $objects = [];
        foreach ($_POST['subscriptionData'] as $object) {
            $objectId = $object['id'] ?? '';
            if (strpos($objectId, '2.9.') === 0) {
                $objects[] = $objectId;
            }
        }
        if (empty($objects)) {
            return new Response('');
        }
        $transactions = $this->bitshares->getObjects($objects);
        $transactionBlocks = [];
        foreach ($transactions as $transactionId => $transactionHistoryObject) {
            $operation = new OperationHelper($this->bitshares->getClient(), $transactionHistoryObject->operation_id->op[0], $transactionHistoryObject->operation_id->op[1]);
            // dump($operation, $transactionHistoryObject->operation_id);
            $r = $this->render('dashboard/transaction.html.twig', [
                'transaction' => $transactionHistoryObject,
                // TODO: REFACTORING NEEDED! this is operation. not transaction description
                'transaction__description' => $operation->getDescription(),
                // 'transaction__description' => '',
                'transaction__operation__helper' => $transactionHistoryObject->operation_id->helper,
                'transaction__operation__trx_in_block' => $transactionHistoryObject->operation_id->trx_in_block,
                'transaction__operation__block_num' => $transactionHistoryObject->operation_id->block_num,
                'transaction__date' => date('d.m.Y H:i:s'),
                'transaction__account__id' => $transactionHistoryObject->account->id ?? '1.2.0',
                'transaction__account__name' => $transactionHistoryObject->account->name ?? 'null',
            ]);
            $transactionBlocks[] = $r->getContent();
        }
        $latestBlock = $this->bitshares->dynamicGlobalProperties->head_block_number;
        $this->getLatest($latestBlock);
        $blockBlocks = [];
        $blockRepo = $this->getDoctrine()->getRepository(Block::class);
        $latestBlocks = $blockRepo->findBy([], ['number' => 'DESC'], static::BLOCK_COUNT);
        // TODO: REFACTOR
        $witnesses = [];
        $transactionsCount = 0;
        // TODO: get time of block and diff from now
        $secondsSpend = static::BLOCK_COUNT * $this->bitshares->globalProperties->parameters->block_interval;
        foreach ($latestBlocks as $block) {
            $witnesses[] = $block->getWitness();
            $transactionsCount += $block->getTransactionsCount();
        }
        $witnesses = $this->bitshares->getObjects($witnesses);
        foreach ($latestBlocks as $block) {
            $b = $this->render('dashboard/block.html.twig', [
                'blockData' => $block,
                'witness' => $witnesses[$block->getWitness()]
            ]);
            $blockBlocks[] = $b->getContent();
        }
        $return = [
            'transactions' => $transactionBlocks,
            'blocks' => $blockBlocks,
            'head_block_number' => number_format($latestBlock, 0, '.', ','),
            'head_block_trx' => $latestBlocks[0]->getTransactionsCount(),
            'trx_per_second' => number_format($transactionsCount / $secondsSpend, 3),
            'trx_per_block' => number_format($transactionsCount / static::BLOCK_COUNT, 2),
        ];
        return new Response(json_encode($return));
    }
}
