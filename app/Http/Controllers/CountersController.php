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
        $get = Counters::with('department')->get();

        return response()->json(['payload' => ['count' => $count, 'data' => $get]], 200);
    }

    public function getByDepartment($department_id){
        $get = Counters::where('department_id','=',$department_id)->get();
        return response()->json(['payload' => ['data' => $get]], 200);
    }

   

    public function store(Request $request)
    {
        
        $request->validate([
            'department_id' => 'required|integer',
            'position' => 'required|integer'
        ]);
        
        $validatedData = [
            'department_id' => $request->input('department_id'),
            'position' => $request->input('position')
        ];

        $create = Counters::create($validatedData);

        $data = Counters::with('department')->findOrFail($create->id);

        return response()->json(['payload' => ['data' => $data]], 201);
    }

    public function update(Request $request, $id)
    {
        $counter = Counters::findOrFail($id);

        $request->validate([
            'department_id' => "required|integer",
            'position' => "required|integer"
        ]);

        $validatedData = [
            'department_id' => $request->input('department_id'),
            'position' => $request->input('position')
        ];

        $counter->update($validatedData);

        $model = Counters::with('department')->findOrFail($id);

        return response()->json([ 'payload' => [ 'data' => $model ]], 200);
    }
}