<?php

namespace App\Controller;

use Bitshares\Object\Protocol\Account;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/account", name="account_")
 */
class AccountController extends BaseBitsharesController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('account/search.html.twig');
    }

    /**
     * @Route("/id/{id}", name="by_id")
     */
    public function byId($id)
    {
        $bitshares = $this->getBitshares();
        $bitshares->initApi('history');
        $account = $bitshares->getObject($id);
        $history = $bitshares->historyApi->get_account_history($account->id, '1.11.0', 100, '1.11.99999999999999999'); // TODO: BAD!!!! Change
        $operationIds = [];
        foreach ($history as $operation) {
            $operationIds[] = $operation->id;
        }
        $balances = $bitshares->statelessApi->get_account_balances($account->id, []);
        $assetIds = [];
        foreach ($balances as $balance) {
            $assetIds[] = $balance->asset_id;
        }
        $data = $bitshares->getObjects(array_merge($assetIds, $operationIds));
        foreach ($history as &$operation) {
            $operation = $data[$operation->id];
        }
        unset($operation);
        $totalValue = 0;
        foreach ($balances as &$balance) {
            $asset = $data[$balance->asset_id];
            $balance->asset = $asset;
            $balance->amount = $balance->amount / pow(10, $asset->precision);
            $exchangeRate = $asset->options->core_exchange_rate;
            $balance->price = $exchangeRate->base->amount / $exchangeRate->quote->amount;
            $balance->value = $balance->amount * $balance->price;
            $totalValue += $balance->value;
        }
        unset($balance);
        return $this->render('account/account.html.twig', [
            'account' => $account,
            'balances' => $balances,
            'totalValue' => $totalValue,
            'history' => $history,
        ]);
    }

    /**
     * @Route("/name/{name}", name="by_name")
     */
    public function byName($name)
    {
        $bitshares = $this->getBitshares();
        $account = $bitshares->statelessApi->lookup_account_names([$name]);
        if (is_null($account[0])) {
            throw new NotFoundHttpException('Account does not exist');
        }
        return $this->byId($account[0]->id);
    }

    /**
     * @Route("/lookup", name="lookup")
     */
    public function lookup(Request $request)
    {
        $result = [];
        $name = $request->get('accountName');
        $name = trim($name);
        if(empty($name)) return new Response(json_encode($result));
        $bitshares = $this->getBitshares();
        $accounts = $bitshares->statelessApi->lookup_accounts($name, 10);
        foreach ($accounts as $account) {
            // if($account[0] === $name) return new Response(json_encode([$account[1] => $account[0]]));
            $result[$account[1]] = $account[0];
        }
        return new Response(json_encode($result));
    }

    /**
     * @Route("/count", name="count")
     */
    public function count()
    {
        $bitshares = $this->getBitshares();
        $count = $bitshares->statelessApi->get_account_count();
        dump($count);
        die();
        $result = [];
        foreach ($accounts as $account) {
            // if($account[0] === $name) return new Response(json_encode([$account[1] => $account[0]]));
            $result[$account[1]] = $account[0];
        }
        return new Response(json_encode($result));
    }
}
