<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Media;

class MediaController extends Controller
{
    
    public function getMedia(){
        
        $medias = Media::where('visibility','=',1)->orderBy('weight','asc')->get();
        $data = [];
        foreach ($medias as $media) {
           $mediaUrl = "http://localhost:8000/storage/$media->source"; //Storage::disk('public')->url($media->media_path); 
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
}