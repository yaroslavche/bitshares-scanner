<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Service\BitsharesClient;

class BaseBitsharesController extends Controller
{
    /**
     * @var BitsharesClient
     */
    protected $bitshares;

    /**
     * @param BitsharesClient $bitshares
     */
    public function __construct(BitsharesClient $bitshares)
    {
        $this->bitshares = $bitshares;
    }

    public function getBitshares() : BitsharesClient
    {
        return $this->bitshares;
    }
}
