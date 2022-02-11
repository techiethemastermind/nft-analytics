<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Token;
use App\Models\Transaction;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

class TokensController extends Controller
{
    protected $baseUrl;
    protected $apiKey;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = env('ETHERSCAN_API_BASE_URL');
        $this->apiKey  = env('ETHERSCAN_API_KEY');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('tokens');
    }

    /**
     * Get Tokens List
     */
    public function getList(Request $request)
    {
        $tokens = Token::all();
        $data = [];
        $i = 1;
        foreach ($tokens as $token) {
            $temp             = [];
            $temp['index']    = $i;
            $temp['token']    = $token->name;
            $temp['contract'] = $token->contract;
            $temp['action']   = '<button class="btn btn-primary btn-sm" data-id="'. $token->id .'">Update Data</button>';
            array_push($data, $temp);
            $i++;
        }

        return response()->json([
            'success' => true,
            'data'    => $data,
            'count'   => $tokens->count()
        ]);
    }

    /**
     * Update Token list by manually
     */
    public function updateTokens(Request $request)
    {
        $goutteClient = new Client(HttpClient::create(['timeout' => 60]));

        for ($i = 1; $i < 20; $i++) {
            $crawler = $goutteClient->request('GET', 'https://etherscan.io/tokens-nft?p=' . $i);

            $response = $crawler->filter('#tblResult tbody tr')->each(function ($node) {

                try {
                    $tokenName = $node->filter('a.text-primary')->text();
                    $tokenHref = $node->filter('a.text-primary')->extract(array('href'))[0];
                    $exploded  = explode('/', $tokenHref);
                    $tokenAddr = end($exploded);
        
                    return [
                        'status'   => true,
                        'name'     => $tokenName,
                        'contract' => $tokenAddr
                    ];
                } catch (\Exception $e) {

                    return [
                        'status'  => false,
                        'code'    => $e->getCode(),
                        'message' => $e->getMessage()
                    ];
                }
            });

            if (count($response) > 0) {

                foreach ($response as $token) {
                    if ($token['status']) {

                        Token::updateOrCreate(
                            [
                                'name'     => $token['name'],
                                'contract' => $token['contract'],
                            ],
                            [
                                'contract' => $token['contract']
                            ]
                        );
                    }
                }
            }
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Update Database
     */
    public function updateTransactions(Request $request) {
        $tokens = Token::all();

        foreach ($tokens as $token) {

            // Check transaction is added or not
            $count = Transaction::where('token_id', $token->id)->count();

            if ($count > 0) {
                continue;
            }

            $response = Http::get($this->baseUrl, [
                'module'            => 'account',
                'action'            => 'tokennfttx',
                'contractaddress'   => $token->contract,
                'startblock'        => 0,
                'endblock'          => 'latest',
                'sort'              => 'desc',
                'apiKey'            => $this->apiKey
            ]);

            $result    = $response->json($key = null);
            $arrResult = $result['result'];
            $symAdded  = false;

            foreach ($arrResult as $transaction) {
                Transaction::create(
                    [
                        'token_id' => $token->id,
                        'hash'     => $transaction['hash'],
                        'from'     => $transaction['from'],
                        'to'       => $transaction['to'],
                        'tokenId'  => $transaction['tokenID'],
                        'time'     => $transaction['timeStamp'],
                        'txIndex'  => $transaction['transactionIndex']
                    ]
                );

                if (!$symAdded) {
                    $token->symbol = $transaction['tokenSymbol'];
                    $token->save();
                    $symAdded = true;
                }
            }
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Update Transaction Details
     */
    public function updateTransactionDetails()
    {
        $transactions = Transaction::all()->chunk(1000);
        
        foreach ($transactions as $subTransactions) {

            foreach($subTransactions as $transaction) {

                if ($transaction->value != NULL) {
                    continue;
                }
                
                $response = Http::get($this->baseUrl, [
                    'module' => 'proxy',
                    'action' => 'eth_getTransactionByHash',
                    'txhash' => $transaction->hash,
                    'apiKey' => $this->apiKey
                ]);

                $result    = $response->json($key = null);
                $arrResult = $result['result'];

                if (!empty($result)) {
                    $value     = hexdec($arrResult['value']) / 1000000000000000000;
                
                    $transaction->value = $value;
                    $transaction->save();
                    sleep(0.2);
                } else {
                    continue;
                }
            }
        }

        return response()->json([
            'success' => true
        ]);
    }


}
