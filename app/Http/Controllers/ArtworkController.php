<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    function index($id)
    {
        $artwork = Artwork::where('id',$id)->first();

        return view('artwork.index', [
            'artwork' => $artwork,
            'medias' => $artwork->medias->sortBy('lft'),
        ]);
    }
}
