<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MuseumController extends Controller
{
    function index(Request $request)
    {
        $museum = Museum::all();
        return view("museum.index", ["museum" => $museum]);
    }

    function create(Request $request)
    {
        return view("museum.form");
    }
}
