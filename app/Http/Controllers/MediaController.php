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
           $mediaUrl =  "http://localhost:8000/storage/$media->source";//Storage::disk('public')->url($media->source); 
           $data[] = [
               'id' => $media->id,
               'src' => $mediaUrl,
               'title' => $media->title,
               'type' => $media->media_type
           ];
        }

        $payload = $data;

        return response()->json(compact('payload'), 200);

    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'media' => 'required|mimetypes:video/mp4|max:25000'
        ]);

        $path = Storage::putFile('public', $request->file('media'));

        $createData = [
            'source' => $path,
            'visibility' => 1,
            'weight' => 1,
            'title' => null
        ];

        Media::create($createData);

        return response()->json(compact('path'));
    }

   
}