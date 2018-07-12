<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Service\BitsharesClient;

/**
 * @Route("/committee", name="committee_")
 */
class CommitteeController extends Controller
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
        $count = $this->bitshares->statelessApi->get_committee_count();
        $committeeIds = [];
        for ($committeeId = 0; $committeeId < $count; $committeeId++) {
            $committeeIds[] = sprintf('1.5.%s', $committeeId);
        }
        $committeeMembers = $this->bitshares->getObjects($committeeIds);
        // dump($committeeMembers, $this->bitshares->globalProperties);
        // die();
        return $this->render('committee/list.html.twig', [
            'committeeMembers' => $committeeMembers,
            'activeCommitteeMembers' => $this->bitshares->globalProperties->active_committee_members
        ]);
    }
}
