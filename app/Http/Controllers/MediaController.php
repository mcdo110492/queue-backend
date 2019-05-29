<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Media;

class MediaController extends Controller
{
    
    public function getMedia()
    {
        
        $medias = Media::where('visibility','=',1)->orderBy('weight','asc')->get();
        $data = [];
        foreach ($medias as $media) {
           $data[] = [
               'id' => $media->id,
               'source' => $media->source,
               'title' => $media->title,
               'media_type' => $media->media_type
           ];
        }

        $payload = $data;

        return response()->json(compact('payload'), 200);

    }

    public function getAll() {
        
        $medias = Media::get();

        $payload = ['data' => $medias];

        return response()->json(compact('payload'), 200);
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'medias' => 'required|mimetypes:video/mp4|max:25000'
        ]);

        $path = Storage::putFile('public', $request->file('medias'));

        $file_name = $this->extractBasename($path);

        $data = [
            'source' => $file_name,
            'visibility' => 1,
            'weight' => 1,
            'title' => ""
        ];

        Media::create($data);
            
    
        
        $payload = ['message' => "File(s) has been successfully uploaded"];
        
        return response()->json(compact('payload'));
    }


    public function updateMeta($id)
    {
        $media = Media::findOrFail($id);

        $response = $response->validate([
            'visibility' => 'required|integer',
            'weight' => 'required|integer',
            'title' => 'required|max:150'
        ]);

        $dataValidated = [
            'visibility' => $request->input('visibility'),
            'weight' => $request->input('weight'),
            'title' => $request->input('title')
        ];

        $media->update($dataValidated);

        $updatedData = Media::findOrFail($id);

        $payload = ['data' => $updatedData];

        return response()->json($payload, 200);
    }


    private function extractBasename($path) {
        $extract = \explode("/", $path);
        return $extract[1];
    }


   
}