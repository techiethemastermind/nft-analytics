<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\HttpClient\HttpClient;
use Carbon\Carbon;
use App\Models\Token;
use App\Models\Transaction;
use App\Models\TransactionItem;

class ScrapController extends Controller
{
    protected $client;
    protected $isStop;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $goutteClient = new Client(HttpClient::create(['timeout' => 60]));
        $goutteClient->setServerParameter('accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');
        $goutteClient->setServerParameter('accept-encoding', 'gzip, deflate, br');
        $goutteClient->setServerParameter('accept-language', 'en-GB,en-US;q=0.9,en;q=0.8');
        $goutteClient->setServerParameter('upgrade-insecure-requests', '1');
        $goutteClient->setServerParameter('user-agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36');
        $goutteClient->setServerParameter('connection', 'keep-alive');
        $this->client = $goutteClient;
        $this->isStop = false;
    }

    /**
     * Scrapping Tokens
     */
    public function scrappingTokens() {

        for ($i = 1; $i < 20; $i++) {
            $crawler = $this->client->request('GET', 'https://etherscan.io/tokens-nft?p=' . $i);

            $response = $crawler->filter('#tblResult tbody tr')->each(function ($node) {

                try {
                    $tokenName = $node->filter('a.text-primary')->text();
                    $tokenHref = $node->filter('a.text-primary')->extract(array('href'))[0];
                    $exploded  = explode('/', $tokenHref);
                    $tokenAddr = end($exploded);
        
                    return [
                        'status'  => true,
                        'name'    => $tokenName,
                        'address' => $tokenAddr,
                        'link'    => $tokenHref
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
                        $rlt = $this->storeTokens($token);
                    }
                }
            }
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Store tokens scrapped
     * @param array $token
     */
    private function storeTokens(array $token)
    {
        $newRecord = Token::updateOrCreate(
            [
                'name'    => $token['name'],
                'address' => $token['address'],
                'link'    => $token['link']
            ],
            [
                'address' => $token['address']
            ]
        );

        return $newRecord;
    }

    /**
     * Scrapping Transactions
     */
    public function scrappingTransactions()
    {
        $tokens = Token::all();
        
        foreach ($tokens as $token) {

            // Get sid
            $crawler = $this->client->request('GET', 'https://etherscan.io' . $token->link);
            
            try {
                // Get Sid to get transactions
                $jsText  = $crawler->filterXPath('//script[contains(.,"sid")]')->text();
                $exp     = "/var sid = (.*?);/";
                preg_match($exp, $jsText, $sidArray);
                $sid     = substr($sidArray[1], 1, -1);

            } catch (\Exception $e) {
                \Log::info('Token Link: ' . $token->link . ', Type:' . 'Getting SID, Message:' . $e->getMessage());
                continue;
            }

            // Get Transaction pages number
            $url  = '/token/generic-tokentxns2?m=normal&contractAddress='. $token->address .'&a=&sid='. $sid .'&p=1';

            try {
                $crawler  = $this->client->request('GET', 'https://etherscan.io' . $url);
                $pagesNum = (int)$crawler->filter('nav ul.pagination li.page-item')->eq(2)->filter('strong')->eq(1)->text();
            } catch (\Exception $e) {
                \Log::info('Token Link: ' . $token->link . ', Type:' . 'Getting PagesNum, Message:' . $e->getMessage());
                continue;
            }

            // Scrapping Transactions
            for ($i = 1; $i < $pagesNum; $i++) {

                if ($this->isStop) {
                    break 2;
                }

                $url     = '/token/generic-tokentxns2?m=normal&contractAddress='. $token->address .'&a=&sid='. $sid .'&p=' . $i;
                $crawler = $this->client->request('GET', 'https://etherscan.io' . $url);
                
                $response = $crawler->filter('table tbody tr')->each(function ($node) {
                    try {
                        $txHash = $node->filter('td')->eq(1)->filter('span.myFnExpandBox_searchVal a')->text();
                        $method = $node->filter('td')->eq(2)->filter('span')->text();
                        $txTime = $node->filter('td')->eq(3)->filter('span')->text();
                        $txFrom = $node->filter('td')->eq(5)->filter('a')->text();
                        $txTo   = $node->filter('td')->eq(7)->filter('a')->text();
                        $tokenId = $node->filter('td')->eq(8)->filter('a')->text();
                        
                        return [
                            'status'  => true,
                            'address' => $txHash,
                            'method'  => $method,
                            'time'    => $txTime,
                            'from'    => $txFrom,
                            'to'      => $txTo,
                            'token'   => $tokenId
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

                    foreach ($response as $transaction) {
                        if ($transaction['status']) {
                            if ($transaction['method'] == 'Atomic Match_' 
                                || $transaction['method'] == 'Transfer From' 
                                || $transaction['method'] == 'Proxy Assert') {
                                    $this->storeTransactionHash($token->id, $transaction);
                            }
                        } else {
                            \Log::info('Link: ' . $token->link . ', Type: Get transaction Data, Page: ' 
                                . $pageNumber . ', Message: ' . $transaction['message']);
                        }
                    }
                } else {
                    dd('dfdfdf');
                }

                // Delay time for 10 seconds
                if (($i % 100) == 0) {
                    sleep(10);
                }
            }
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Update Transactions
     */
    public function updatingTransactions()
    {
        $tokens = Token::all();
        
        foreach ($tokens as $token) {

            // Get sid
            $crawler = $this->client->request('GET', 'https://etherscan.io' . $token->link);
            
            try {
                // Get Sid to get transactions
                $jsText  = $crawler->filterXPath('//script[contains(.,"sid")]')->text();
                $exp     = "/var sid = (.*?);/";
                preg_match($exp, $jsText, $sidArray);
                $sid     = substr($sidArray[1], 1, -1);

            } catch (\Exception $e) {
                \Log::info('Token Link: ' . $token->link . ', Type:' . 'Getting SID, Message:' . $e->getMessage());
                continue;
            }

            // Get Transaction pages number
            $url = '/token/generic-tokentxns2?m=normal&contractAddress='. $token->address .'&a=&sid='. $sid .'&p=1';

            try {
                $crawler  = $this->client->request('GET', 'https://etherscan.io' . $url);
                $pagesNum = (int)$crawler->filter('nav ul.pagination li.page-item')->eq(2)->filter('strong')->eq(1)->text();
            } catch (\Exception $e) {
                \Log::info('Token Link: ' . $token->link . ', Type:' . 'Getting PagesNum, Message:' . $e->getMessage());
                continue;
            }

            $isUpdated = false;

            // Scrapping Transactions
            for ($i = 1; $i < $pagesNum; $i++) {

                if ($this->isStop) {
                    break 2;
                }

                if ($isUpdated) {
                    break;
                }

                $url     = '/token/generic-tokentxns2?m=normal&contractAddress='. $token->address .'&a=&sid='. $sid .'&p=' . $i;
                $crawler = $this->client->request('GET', 'https://etherscan.io' . $url);
                
                $response = $crawler->filter('table tbody tr')->each(function ($node) {
                    try {
                        $txHash = $node->filter('td')->eq(1)->filter('span.myFnExpandBox_searchVal a')->text();
                        $method = $node->filter('td')->eq(2)->filter('span')->text();
                        $txTime = $node->filter('td')->eq(3)->filter('span')->text();
                        $txFrom = $node->filter('td')->eq(5)->filter('a')->text();
                        $txTo   = $node->filter('td')->eq(7)->filter('a')->text();
                        $tokenId = $node->filter('td')->eq(8)->filter('a')->text();
                        
                        return [
                            'status'  => true,
                            'address' => $txHash,
                            'method'  => $method,
                            'time'    => $txTime,
                            'from'    => $txFrom,
                            'to'      => $txTo,
                            'token'   => $tokenId
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

                    foreach ($response as $transaction) {
                        if ($transaction['status']) {
                            // Check already in DB or not
                            $count = Transaction::where('time', '>', $transaction['time'])->count();

                            if ($count < 1) {
                                $this->storeTransactionHash($token->id, $transaction);
                            } else {
                                $isUpdated = true;
                            }
                            
                        } else {
                            \Log::info('Link: ' . $token->link . ', Type: Get transaction Data, Page: ' 
                                . $pageNumber . ', Message: ' . $transaction['message']);
                        }
                    }
                }
            }
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Store Transaction Hash to DB
     * 
     * @param string $tokenId
     * @param array  $transaction
     */
    private function storeTransactionHash($tokenId, $transaction)
    {
        Transaction::create(
            [
                'token_id' => $tokenId,
                'address'  => $transaction['address'],
                'link'     => '/tx/' . $transaction['address'],
                'method'   => $transaction['method'],
                'from'     => $transaction['from'],
                'to'       => $transaction['to'],
                'token'    => $transaction['token'],
                'time'     => $transaction['time']
            ]
        );
    }

    /**
     * Scrapping Transaction Items
     */
    public function scrappingTransactionItems()
    {
        $tokens = Token::all();

        foreach ($tokens as $token) {

            $transactions = $token->transactions->where('status', '!=', 1);
            $loop = 1;

            foreach ($transactions as $transaction) {

                if ($this->isStop) {
                    break 2;
                }
    
                $crawler = $this->client->request('GET', 'https://etherscan.io' . $transaction->link);
                $response = $crawler->filter('#home')->each(function ($node) {

                    try {
                        // Get Status
                        $status  = $node->filter('div.row span.u-label')->text(); // result
    
                        // Get Timestamp
                        $timeStr = $node->filter('#ContentPlaceHolder1_divTimeStamp div.col-md-9')->text();
                        $exp     = "/\((.*?)\)/";
                        preg_match($exp, $timeStr, $match);
                        $timeStr = str_replace('+', '', $match[1]);
                        $timeStamp = Carbon::parse($timeStr)->timestamp; // result
    
                        // Get transaction Action
                        $typeStr = $node->filter('#wrapperContent li div.media-body span')->eq(0)->text();
                        $type    = substr($typeStr, 0, -1); // result
    
                        if ($type == 'Sale') {
                            $nftStr = $node->filter('#wrapperContent li div.media-body span')->eq(1)->text();
                            preg_match('/\d+/', $nftStr, $matches);
                            $nft    = floatval($matches[0]); // NFT
        
                            $price  = $node->filter('#wrapperContent li div.media-body span')->eq(3)->text();
    
                            // Get From
                            $fromStr = $node->filter('#wrapperContent div li div.media-body span.hash-tag a')->eq(0)->text();
                            $toStr   = $node->filter('#wrapperContent div li div.media-body span.hash-tag a')->eq(1)->text();
                            $tokenId = $node->filter('#wrapperContent div li div.media-body span.hash-tag a')->eq(2)->text();
    
                            // Get Transaction Detail
                            $amount1Str = $node->filter('#wrapperContent li.media div.small')->eq(0)->text();
                            preg_match('/\d+.\d+/', $amount1Str, $matches);
                            $amount1 = floatval($matches[0]);
    
                            $amount2Str = $node->filter('#wrapperContent li.media div.small')->eq(1)->text();
                            preg_match('/\d+.\d+/', $amount2Str, $matches);
                            $amount2 = floatval($matches[0]);
    
                        } else if ($type == 'Transfer o') {
                            $type    = 'N/A';
                            $nft     = 0;
                            $price   = 0;
                            $fromStr = $node->filter('#wrapperContent li div.media-body span.hash-tag a')->eq(0)->text();
                            $toStr   = $node->filter('#wrapperContent li div.media-body span.hash-tag a')->eq(1)->text();
                            $tokenId = $node->filter('#wrapperContent li div.media-body span.hash-tag a')->eq(2)->text();
                            $amount1 = 0;
                            $amount2 = 0;
                        } else {
                            return [
                                'status'  => false,
                                'message' => 'Mint of'
                            ];
                        }

                        return [
                            'status'    => true,
                            'action'    => $status,
                            'timestamp' => $timeStamp,
                            'type'      => $type,
                            'nft'       => $nft,
                            'price'     => $price,
                            'from'      => $fromStr,
                            'to'        => $toStr,
                            'tokenId'   => $tokenId,
                            'amount1'   => $amount1,
                            'amount2'   => $amount2
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
    
                    if ($response[0]['status']) {
                        $this->storeTransactionItem($transaction, $response[0]);
                    } else {
                        Transaction::where('id', $transaction->id)->update(['status' => 2, 'error' => $response[0]['message']]);
                    }
                } else {
                    Transaction::where('id', $transaction->id)->update(['status' => 0, 'error' => 'no response']);
                }
    
                $loop++;
    
                if (($loop % 25) == 0) {
                    sleep(20);
                }
            }
        }
    }

    /**
     * Store Transaction Data
     * 
     * @param object $transactionId
     * @param array  $data
     */
    private function storeTransactionItem($transaction, $data)
    {
        TransactionItem::updateOrCreate(
            [
                'transaction_id' => $transaction->id,
                'address'         => $transaction->address,
                'status'          => $data['action'],
                'timestamp'       => $data['timestamp'],
                'type'      => $data['type'],
                'nft'       => $data['nft'],
                'price'     => $data['price'],
                'from'      => $data['from'],
                'to'        => $data['to'],
                'token_id'  => $data['tokenId'],
                'amount1'   => $data['amount1'],
                'amount2'   => $data['amount2']
            ],
            [
                'address'   => $transaction->address,
            ]
        );

        Transaction::where('id', $transaction->id)->update(['status' => 1]);
    }

    /**
     * Stop Scrapping
     */
    public function stopScrapping()
    {
        $this->isStop = true;

        return response()->json([
            'success' => true
        ]);
    }
}
