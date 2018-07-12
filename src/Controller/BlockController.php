<?php

namespace App\Controller;

use Bitshares\Object\Protocol\OperationHelper;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/block", name="block_")
 */
class BlockController extends BaseBitsharesController
{
    /**
     * @Route("/{number}", name="by_number")
     */
    public function byNumber($number)
    {
        $number = (int)$number;
        $block = $this->bitshares->databaseApi->get_block($number);
        if (!$block) {
            throw new NotFoundHttpException('The block does not exist');
        }
        foreach ($block->transactions as &$transaction) {
            // TODO: $operation = new OperationHelper($type, $data);
            $operation = new OperationHelper($this->bitshares->getClient(), $transaction->operations[0][0], $transaction->operations[0][1]);
            $transaction->operationFee = $operation->getFee();
            $transaction->operationTitle = $operation->getTypeTitle();
            $transaction->operationDescription = $operation->getDescription();
        }
        unset($transaction);
        $blockData = [
            'number' => $number,
            'previous' => $block->previous,
            'date' => date('d.m.Y H:i:s', strtotime($block->timestamp)),
            'witness' => $block->witness,
            'transactions' => $block->transactions,
        ];
        return $this->render('block/block.html.twig', [
            'block' => $blockData,
        ]);
    }
}
