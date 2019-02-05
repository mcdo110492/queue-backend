<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Counters;
use Validator;

class CountersController extends Controller
{

  

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => 'getAll']);
    }


    public function getAll()
    {
        $count = Counters::count();
        $get = Counters::all();

        return response()->json(['payload' => ['count' => $count, 'data' => $get]], 200);
    }

    public function checkUniqueValue(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $id = $request->input('id');

        $table = 'counters';

        $validate = $this->validateUniqueValue($table, $field, $value, $id);

        return response()->json($validate, $validate['status']);
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'counter_name' => 'required|max:50|unique:counters,counter_name',
            'position' => 'required|integer|unique:counters,position'
        ]);
        
        $validatedData = [
            'counter_name' => $request->input('counter_name'),
            'position' => $request->input('position')
        ];

        $create = Counters::create($validatedData);

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

        $model = Counters::findOrFail($id);

        return response()->json([ 'payload' => [ 'data' => $model ]], 200);
    }
}