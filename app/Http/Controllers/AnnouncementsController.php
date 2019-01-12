<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Announcements;

class AnnouncementsController extends Controller
{
    const CREATE_STATUS = 201;
    const OK_STATUS = 200;
    const CREATE_STATUS_MESSAGE = "Announcement Created";
    const UPDATE_STATUS_MESSAGE = "Announcement Updated";

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => 'getWithPagination']);
    }


    public function getWithPagination(Request $request)
    {
        $take = $request->input('take');
        $page = $request->input('page') - 1;
        $offset = $take * $page;
        $order = $request->input('order');
        $column = $request->input('column');
        $q = $request->input('q');

        $count = Announcements::count();

        $get = Announcements::where($column,'LIKE', "%$q%")
            ->take($take)
            ->skip($offset)
            ->orderBy($column, $order)
            ->get();

        return response()->json(['status' => self::OK_STATUS, 'payload' => ['count' => $count, 'data' => $get]]);
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

        Announcements::create($validatedData);

        return response()->json(['status' => self::CREATE_STATUS, 'payload' => self::CREATE_STATUS_MESSAGE]);
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

        return response()->json(['status' => self::OK_STATUS, 'payload' => self::UPDATE_STATUS_MESSAGE]);
    }
}