<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;

class ConfigController extends Controller
{
    public function index()
    {
        return view('config');
    }

    /**
     * Store configuration
     */
    public function store(Request $requests)
    {
        foreach ($requests->all() as $key => $value) {
            if ($key != '_token') {
                $key = str_replace('__', '.', $key);
                $config = Config::firstOrCreate(['key' => $key]);
                if($value !== null) {
                    $config->value = $value;
                }
                $config->save();
            }
        }

        return response()->json([
            'success' => true,
            'action' => 'update'
        ]);
    }
}
