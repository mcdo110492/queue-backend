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

    public function store(Request $request)
    {
        
        //Validate data through request valide method
        $request->validate([
            'title' => 'required|max:50',
            'details' => 'required|max:200',
            'weight' => 'required|integer',
            'visibility' => 'required|integer'
        ]);
        
        $validatedData = [
            'title' => $request->input('title'),
            'details' => $request->input('details'),
            'weight' => $request->input('weight'),
            'visibility' => $request->input('visibility')
        ];

        $create = Announcements::create($validatedData);

        return response()->json(['payload' => $create], 201);
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcements::findOrFail($id);

        $request->validate([
            'title' => 'required|max:50',
            'details' => 'required|max:200',
            'weight' => 'required|integer',
            'visibility' => 'required|integer'
        ]);

        
        $validatedData = [
            'title' => $request->input('title'),
            'details' => $request->input('details'),
            'weight' => $request->input('weight'),
            'visibility' => $request->input('visibility')
        ];

        $announcement->update($validatedData);

        $updatedData = Announcements::findOrFail($id);

        return response()->json(['payload' => $updatedData], 200);
    }
}