<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Returns the view for the frontend page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('frontpage');
    }
    public function about()
    {
        return view('about');
    }
    public function docs()
    {
        return view('docs');
    }
    public function disclaimer()
    {
        return view('disclaimer');
    }
}
