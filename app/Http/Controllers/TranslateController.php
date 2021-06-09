<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Translate\V2\TranslateClient;

class TranslateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function translate(Request $request)
    {
        $params = $request->all();

        $translate = new TranslateClient([
            'key' => config('app.google_api_key')
        ]);

        $result = $translate->translate($params['text'], [
            'target' => 'en'
        ]);
        
        return response()->json([
            'success' => 1,
            'result' => $result['text'],
        ]);
    }
}
