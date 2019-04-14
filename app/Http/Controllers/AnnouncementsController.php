<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Announcements;

class AnnouncementsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => 'getWithPagination']);
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
            'schedule_date' => 'date|nullable',
            'visibility' => 'required|integer'
        ]);
        $schedule_date_input = $request->input('schedule_date');

        $schedule_date = ($schedule_date_input != null) ? Carbon::parse($schedule_date_input) : NULL;
        
        $validatedData = [
            'title' => $request->input('title'),
            'details' => $request->input('details'),
            'weight' => $request->input('weight'),
            'visibility' => $request->input('visibility'),
            'schedule_date' => $schedule_date
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
            'schedule_date' => 'date|nullable',
            'visibility' => 'required|integer'
        ]);

        $schedule_date_input = $request->input('schedule_date');
        $schedule_date = ($schedule_date_input != null) ? Carbon::parse($schedule_date_input) : NULL;
        
        $validatedData = [
            'title' => $request->input('title'),
            'details' => $request->input('details'),
            'weight' => $request->input('weight'),
            'visibility' => $request->input('visibility'),
            'schedule_date' => $schedule_date
        ];

        $announcement->update($validatedData);

        $updatedData = Announcements::findOrFail($id);

        return response()->json(['payload' => $updatedData], 200);
    }
}