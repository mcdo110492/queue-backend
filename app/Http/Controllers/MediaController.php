<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Media;

class MediaController extends Controller
{
    
    public function getImages(){
        
        $medias = Media::where('visibility','=',1)->get();
        $data = [];
        foreach ($medias as $media) {
           $mediaUrl = Storage::url($media->media_path);
           $data[] = [
               'media_path' => $mediaUrl,
               'id' => $media->id,
               'weight' => $media->weight
           ];
        }

    }
}