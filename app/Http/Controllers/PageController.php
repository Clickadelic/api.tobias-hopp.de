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
    public function termsofservice()
    {
        return view('terms-of-service');
    }
    public function termsofuse()
    {
        return view('terms-of-use');
    }
    public function termsofprivacy()
    {
        return view('terms-of-privacy');
    }
    public function disclaimer()
    {
        return view('disclaimer');
    }
    public function cookiepolicy()
    {
        return view('cookie-policy');
    }

}
