<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Museum;

class MuseumController extends Controller
{
    function index(Request $request)
    {
        $museum = Museum::all();
        $museum = $museum->first();
        return view("museum.index", ["museum" => $museum]);
    }

    function create(Request $request)
    {
        return view("museum.form");
    }
}
