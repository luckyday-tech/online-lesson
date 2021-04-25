<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VideoChatManager;

class HomeController extends Controller
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
    public function index(Request $request)
    {
        $params = $request->all();

        if (empty($params['room_id']) || empty($params['host_id'])) {
            return redirect()->route('home', [
                'room_id'=> VideoChatManager::generatePeerId(),
                'host_id'=> VideoChatManager::generatePeerId(),
                'is_host'=> 1,
            ]);
        }

        return view('home', [
            'room_id'=> $params['room_id'],
            'host_id'=> $params['host_id'],
            'is_host'=> (!empty($params['is_host']))?$params['is_host']:0,
        ]);
    }
}
