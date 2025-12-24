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
}
