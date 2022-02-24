<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;

class ConfigController extends Controller
{
    public function index()
    {
        $config = Config::where('key', 'tt.values')->first();
        $ttValues = $config->value;
        return view('config', compact('ttValues'));
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
                    $config->value = json_encode($value);
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
