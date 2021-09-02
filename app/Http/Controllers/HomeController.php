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

        $teacher = User::where('type', HOST_TYPE_TEACHER)->first();
        if (empty($teacher)) {
            die('no teacher in this room');
        }

        $personList = [];
        $studentInfos = [];
        $studentList = User::where('type', HOST_TYPE_STUDENT)->get();
        foreach($studentList as $student) {
            $studentInfos[] = [
                'id' => $student['id'],
                'name' => $student['name'],
                'type' => 1,
            ];
        }
        $personList['teacher'] = [
            'id' => $teacher->id,
            'name' => $teacher->name,
        ];
        $personList['student'] = $studentInfos;



        return view('home', [
            'hostType' => $user->type,
            'roomId' => 'r_1_' . $params['room_id'],
            'teacherId' => 't_' . $teacher->id . 'z' . $params['room_id'],
            'studentId' => 's_1_' . $user->id . 'z' . $params['room_id'],
            'studentType' => 1,
            'selfName' => $user->name,
            'lessonTitle' => "Test Lesson",
            'personList' => $personList,
        ]);
    }
}
