<?php

namespace App\Controller;

use Bitshares\Object\Protocol\OperationHistoryHelper;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/fee_shedule", name="fee_schedule_")
 */
class FeeSheduleController extends BaseBitsharesController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $bitshares = $this->getBitshares();
        $fees = $bitshares->globalProperties->parameters->current_fees->parameters;
        $scale = $bitshares->globalProperties->parameters->current_fees->scale; // wrong?
        $scale = 100000;
        foreach ($fees as &$fee) {
            $feeValue = isset($fee[1]->fee) ? $fee[1]->fee : (isset($fee[1]->basic_fee) ? $fee[1]->basic_fee : 0);
            $fee[1]->fee = $feeValue / $scale;
            $fee[1]->premium_fee = isset($fee[1]->premium_fee) ? $fee[1]->premium_fee / $scale : '';
            $fee[1]->price_per_kbyte = isset($fee[1]->price_per_kbyte) ? $fee[1]->price_per_kbyte / $scale : '';
            $operationAlias = OperationHistoryHelper::getOperationTypeAlias($fee[0]);
            $fee[2] = [
                'alias' => strtolower($operationAlias),
                'title' => ucwords(str_replace('_', ' ', strtolower($operationAlias)))
            ];
        }
        unset($fee);
        return $this->render('fee_shedule/index.html.twig', [
            'fees' => $fees
        ]);
    }
}
