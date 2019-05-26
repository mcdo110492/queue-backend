<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UsersCounters;

class UsersCountersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => 'getAll']);
    }


    public function getAll() {
        
        $get = UsersCounters::with(['user','counter.department'])->get();

        return response()->json(['payload' => ['data' => $get]], 200);

    }


    public function checkUniqueValue(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $id = $request->input('id');

        $table = 'users_counters';

        $validate = $this->validateUniqueValue($table, $field, $value, $id);

        return response()->json($validate, $validate['status']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'counter_id' => 'required|integer|exists:counters,id|unique:users_counters,counter_id',
            'user_id' => 'required|integer|exists:users,id|unique:users_counters,user_id'
        ]);
        
        $validatedData = [
            'counter_id' => $request->input('counter_id'),
            'user_id' => $request->input('user_id')
        ];

        $create = UsersCounters::create($validatedData);

        $data = UsersCounters::with(['user','counter.department'])->findOrFail($create->id);

        return response()->json(['payload' => ['data' => $data]], 201);
    }

    public function update(Request $request, $id)
    {
        $counter = UsersCounters::findOrFail($id);

        $request->validate([
            'counter_id' => "required|integer|unique:users_counters,counter_id,$id",
            'user_id' => "required|integer|unique:users_counters,user_id,$id"
        ]);

        $validatedData = [
            'counter_id' => $request->input('counter_id'),
            'user_id' => $request->input('user_id')
        ];

        $counter->update($validatedData);

        $updatedData = UsersCounters::with(['user','counter.department'])->findOrFail($id);

        return response()->json(['payload' => [ 'data' => $updatedData ]], 200);
    }
}