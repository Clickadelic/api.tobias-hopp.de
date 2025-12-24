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
    
    /**
     * Display the login page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function login()
    {
        return view('login');
    }

    /**
     * Displays the logout page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function logout()
    {
        return view('logout');
    }
}
