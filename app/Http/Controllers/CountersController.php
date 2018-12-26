<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Counters;

class CountersController extends Controller
{

    const CREATE_STATUS = 201;
    const OK_STATUS = 200;
    const CREATE_STATUS_MESSAGE = "Counter Created";
    const UPDATE_STATUS_MESSAGE = "Counter Updated";


    public function getWithPagination(Request $request)
    {
        $take = $request->input('take');
        $page = $request->input('page') - 1;
        $offset = $take * $page;
        $order = $request->input('order');
        $column = $request->input('column');
        $q = $request->input('q');

        $count = Counters::count();

        $get = Counters::where($column,'LIKE', "%$q%")
            ->take($take)
            ->skip($offset)
            ->orderBy($column, $order)
            ->get();

        return response()->json(['status' => self::OK_STATUS, 'payload' => ['count' => $count, 'data' => $get]]);
    }

    public function store(Request $request)
    {
        //Validate data through request valida method
        $request->validate([
            'counter_name' => 'required|max:50|unique:counters,counter_name',
            'position' => 'required|integer|unique:counters,position'
        ]);
        
        $validatedData = [
            'counter_name' => $request->input('counter_name'),
            'position' => $request->input('position')
        ];

        Counters::create($validatedData);

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
