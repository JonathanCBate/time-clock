<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Create a new controller instance.
     *
     * This constructor applies the 'auth' middleware to ensure that
     * only authenticated users can access the methods within this controller.
     */
   

    /**
     * Show the application dashboard.
     *
     * This method returns the 'home' view, which serves as the dashboard
     * for authenticated users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('home');
    }
    
}
