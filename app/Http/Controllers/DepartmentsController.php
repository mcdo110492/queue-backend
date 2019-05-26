<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Departments;
use Validator;

class DepartmentsController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => 'getAll']);
    }


    public function getAll()
    {
        $count = Departments::count();
        $get = Departments::all();

        return response()->json(['payload' => ['count' => $count, 'data' => $get]], 200);
    }

    public function checkUniqueValue(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $id = $request->input('id');

        $table = 'departments';

        $validate = $this->validateUniqueValue($table, $field, $value, $id);

        return response()->json($validate, $validate['status']);
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|max:50|unique:departments,name',
            'code' => 'required|integer|unique:departments,code'
        ]);
        
        $validatedData = [
            'name' => $request->input('name'),
            'code' => $request->input('code')
        ];

        $create = Departments::create($validatedData);

        return response()->json(['payload' => ['data' => $create]], 201);
    }

    public function update(Request $request, $id)
    {
        $department = Departments::findOrFail($id);

        $request->validate([
            'name' => "required|max:50|unique:departments,name,$id",
            'code' => "required|integer|unique:departments,code,$id"
        ]);

        $validatedData = [
            'name' => $request->input('name'),
            'code' => $request->input('code')
        ];

        $department->update($validatedData);

        $model = Departments::findOrFail($id);

        return response()->json([ 'payload' => [ 'data' => $model ]], 200);
    }
}