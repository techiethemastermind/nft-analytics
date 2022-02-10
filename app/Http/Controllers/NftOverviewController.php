<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NftOverviewController extends Controller
{
    /**
     * List for NFT Total Overview
     */
    public function index()
    {
        return view('nft-overview');
    }
}
