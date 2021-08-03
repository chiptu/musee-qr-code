<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    function index(Request $request,$id)
    {
        $artwork = Artwork::where('id',$id)->first();

        $ghost = $request->get("ghost");
        if ($ghost != 1 ){
            $artwork->viewNumber = $artwork->viewNumber +1;
        }

        $artwork->save();

        return view('artwork.index', [
            'artwork' => $artwork,
            'medias' => $artwork->medias->sortBy('lft'),
        ]);
    }
}
