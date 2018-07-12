<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Service\BitsharesClient;

/**
 * @Route("/witness", name="witness_")
 */
class WitnessController extends Controller
{
    /**
     * @var BitsharesClient
     */
    private $bitshares;

    /**
     * @param BitsharesClient $bitshares
     */
    public function __construct(BitsharesClient $bitshares)
    {
        $this->bitshares = $bitshares;
    }

    /**
     * @Route("/", name="index")
     */
    public function list()
    {
        $witnessCount = $this->bitshares->statelessApi->get_witness_count();
        $witnessIds = [];
        for ($witnessId = 0; $witnessId < $witnessCount; $witnessId++) {
            $witnessIds[] = sprintf('1.6.%s', $witnessId);
        }
        $witnesses = $this->bitshares->getObjects($witnessIds);
        foreach ($witnesses as $id => $obj) {
            if (is_null($obj)) {
                unset($witnesses[$id]);
                continue;
            }
        }
        $currentWitness = $this->bitshares->getObject($this->bitshares->dynamicGlobalProperties->current_witness->id);
        // sort by total_votes
        // usort($witnesses, function($a, $b)
        // {
        //     return -1 * strcmp((int)$a->total_votes, (int)$b->total_votes);
        // });
        return $this->render('witness/list.html.twig', [
            'witnesses' => $witnesses,
            'activeWitnesses' => $this->bitshares->globalProperties->active_witnesses,
            'currentWitness' => $currentWitness
        ]);
    }
}
