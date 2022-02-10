<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Token;

class TokensController extends Controller
{
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
            $temp = [];
            $temp['index'] = $i;
            $temp['token'] = $token->name;
            $temp['address'] = $token->address;
            $temp['action'] = '<button class="btn btn-primary btn-sm" data-id="'. $token->id .'">Update Data</button>';
            array_push($data, $temp);
            $i++;
        }

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    /**
     * Update Token Data
     */
    public function updateTokenData(Request $request)
    {
       
    }

    /**
     * Update Database
     */
    public function updateDatabase(Request $request) {
        $apiKey = 'EE39G8PIHBRV7FH9MQZ3PDHM4YTEENHAQT';
        $tokens = Token::all();

        foreach ($tokens as $token) {
            $baseUrl  = 'https://api.etherscan.io/api/';
            $response = Http::get($baseUrl, [
                'module' => 'account',
                'action' => 'txlist',
                'startblock' => 0,
                'endblock' => 99999999,
                'address' => $token->address,
                'sort'    => 'desc',
                'apiKey'  => $apiKey,
                'page' => 1,
                'offset' => 1
            ]);
            $result = $response->json($key = null);
            dd($result);
        }
    }
}
