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


    public function getAll()
    {

        $count = UsersCounters::count();

        $get = UsersCounters::with(['user','counter'])->get();

        return response()->json(['payload' => ['count' => $count, 'data' => $get]], 200);
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

        return response()->json(['payload' => ['data' => $create]], 201);
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

        $updatedData = Counters::findOrFail($id);

        return response()->json(['payload' => [ 'data' => $updatedData ]], 200);
    }
}