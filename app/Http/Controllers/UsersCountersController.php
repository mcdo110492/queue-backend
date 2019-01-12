<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\UsersCounters;

class UsersCountersController extends Controller
{
    const CREATE_STATUS = 201;
    const OK_STATUS = 200;
    const CREATE_STATUS_MESSAGE = "Counter Created";
    const UPDATE_STATUS_MESSAGE = "Counter Updated";

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => 'getAll']);
    }


    public function getAll()
    {

        $count = UsersCounters::count();

        $get = UsersCounters::with(['user','counter'])->get();

        return response()->json(['status' => self::OK_STATUS, 'payload' => ['count' => $count, 'data' => $get]]);
    }

    public function store(Request $request)
    {
        //Validate data through request valida method
        $request->validate([
            'counter_id' => 'required|integer|exists:counters,id|unique:users_counters,counter_id',
            'user_id' => 'required|integer|exists:users,id|unique:users_counters,user_id'
        ]);
        
        $validatedData = [
            'counter_id' => $request->input('counter_id'),
            'user_id' => $request->input('user_id')
        ];

        UsersCounters::create($validatedData);

        return response()->json(['status' => self::CREATE_STATUS, 'payload' => self::CREATE_STATUS_MESSAGE]);
    }

    public function update(Request $request, $id)
    {
        $counter = Counters::findOrFail($id);

        $request->validate([
            'counter_name' => "required|max:50|unique:counters,counter_name,$id",
            'position' => "required|integer|unique:counters,position,$id"
        ]);

        $validatedData = [
            'counter_name' => $request->input('counter_name'),
            'position' => $request->input('position')
        ];

        $counter->update($validatedData);

        return response()->json(['status' => self::OK_STATUS, 'payload' => self::UPDATE_STATUS_MESSAGE]);
    }
}