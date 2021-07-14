<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OnlineUser;
use App\Models\Room;
use App\Models\User;

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

        if (empty($params["room_id"]) || empty($params['user_id'])) {
            die('Invalid Params');
        }

        $room = Room::find($params["room_id"]);
        $user = User::find($params["user_id"]);

        if (empty($room) || empty($user)) {
            die('Invalid user');
        }

        return view('home', [
            'room'=> $room,
            'user'=> $user,
        ]);
    }
}
