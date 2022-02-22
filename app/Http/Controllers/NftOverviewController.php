<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Token;
use App\Models\Transaction;
use Carbon\Carbon;

class NftOverviewController extends Controller
{
    /**
     * List for NFT Total Overview
     */
    public function index()
    {
        return view('nft-overview');
    }

    /**
     * Get Overview Table Data
     */
    public function getTableData()
    {
        $tokens  = Token::skip(0)->take(3)->get();
        $ttValue = 15;  // TT value
        $tt      = 3;   // TT count
        
        $data = [];
        $loop = 1;
        
        foreach ($tokens as $token) {
            $temp = [];
            $txFirst   = $token->transactions()->take(100)->orderBy('time', 'desc')->first();
            $timeTo    = (int)$txFirst->time;
            $timeFrom  = $timeTo - $ttValue * 60;
            $nftValues = $this->getNftOverviewValues($token->id, $timeFrom, $timeTo);

            // get TT in the case of TT = 3 and TT value= 15 min
            $ttTotal   = 0;
            $tfTotal   = 0;
            for ($i = 0; $i < $tt; $i++) {

                $ttTimeFrom  = $timeFrom - ($i * $ttValue);
                $ttTimeTo    = $timeTo - ($i * $ttValue);
                $ttNftValues = $this->getNftOverviewValues($token->id, $ttTimeFrom, $ttTimeTo);
                $ttTotal += $ttNftValues['totalSale'];
                $tfTotal += $ttNftValues['floorValue'];
            }
            
            $ttAverage = $ttTotal / $tt;
            $tfAverage = $tfTotal / $tt;
            $ttResult  = ($nftValues['totalSale'] > $ttAverage) ? 'UP' : 'DN';
            $tfResult  = ($nftValues['floorValue'] > $tfAverage) ? 'UP' : 'DN';

            $temp['index'] = $loop;
            $temp['token'] = $token->name;
            $eleTT = 'T' . $nftValues['totalSale'] . ' / ' . $ttResult . ' <br> WF' . $nftValues['wfValue'];
            $eleTF = 'FL' . $nftValues['floorValue'] . ' / ' . $tfResult . ' <br> AV' . $nftValues['averagePrice'];
            $temp['result'] = '<div class="left">' . $eleTT . '</div><div class="right">' . $eleTF . '</div>';
            $loop++;
            array_push($data, $temp);
        }

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * @param string   $tokenId
     * @param integer  $timeFrom
     * @param integer  $timeTo
     * 
     * @return array
     */
    private function getNftOverviewValues($tokenId, $timeFrom, $timeTo): array
    {
        $transactions = Transaction::select(['value', 'to'])
            ->where('token_id', $tokenId)
            ->whereBetween('time', [$timeFrom, $timeTo])
            ->limit(100)->get();
        $totalSale    = $transactions->count(); // T
        $averagePrice = $transactions->avg('value'); // AV
        $floorValue   = $transactions->min('value'); // Floor
        $wfValue      = 1;

        if ($transactions->groupBy('to')->count() != 0) {
            $wfValue  = $totalSale / $transactions->groupBy('to')->count();  // WF
        }

        return [
            'totalSale'    => $totalSale,
            'averagePrice' => $averagePrice,
            'wfValue'      => $wfValue,
            'floorValue'   => $floorValue
        ];
    }
}
