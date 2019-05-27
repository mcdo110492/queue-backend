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
               'src' => $media->source,
               'title' => $media->title,
               'type' => $media->media_type
           ];
        }

        $payload = $data;

        return response()->json(compact('payload'), 200);

    }

    public function getAll() {
        
        $medias = Media::get();

        $payload = ['data' => $medias];

        return response()->json($payload);
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
            'title' => $request->file('medias')->getClientOriginalName()
        ];

        Media::create($data);
            
    
        
        $payload = ['message' => "File(s) has been successfully uploaded"];
        
        return response()->json(compact('payload'));
    }

    private function extractBasename($path) {
        $extract = \explode("/", $path);
        return $extract[1];
    }

   
}