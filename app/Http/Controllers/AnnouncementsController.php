<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Announcements;

class AnnouncementsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAll', 'getAllVisible']]);
    }

    public function getAll(){

        $get = Announcements::get();

        return response()->json(['payload' => ['data' => $get]], 200);
    }


    public function getAllVisible(){
       
        $get = Announcements::where('visibility','=',1)->orderBy('weight','ASC')->get();

        return response()->json(['payload' => ['data' => $get]], 200);
    }

    public function checkUniqueValue(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $id = $request->input('id');

        $table = 'announcements';

        $validate = $this->validateUniqueValue($table, $field, $value, $id);

        return response()->json($validate, $validate['status']);
    }

    public function store(Request $request)
    {
        
        //Validate data through request valide method
        $request->validate([
            'message' => 'required|max:250',
            'weight' => 'required|integer',
            'visibility' => 'required|integer'
        ]);
        
        $validatedData = [
            'message' => $request->input('message'),
            'weight' => $request->input('weight'),
            'visibility' => $request->input('visibility')
        ];

        $create = Announcements::create($validatedData);

        return response()->json(['payload' => ['data' => $create]], 201);
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcements::findOrFail($id);

        $request->validate([
            'message' => 'required|max:250',
            'weight' => 'required|integer',
            'visibility' => 'required|integer'
        ]);

        
        $validatedData = [
            'message' => $request->input('message'),
            'weight' => $request->input('weight'),
            'visibility' => $request->input('visibility')
        ];

        $announcement->update($validatedData);

        $updatedData = Announcements::findOrFail($id);

        return response()->json(['payload' => ['data' => $updatedData]], 200);
    }
}