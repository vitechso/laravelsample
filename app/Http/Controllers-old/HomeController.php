<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome');
    }

    public function index123()
    {
         $counts = [
            'users' => 0,
            'users_unconfirmed' => 0,
            'users_inactive' => 0,
            'protected_pages' => 0,
        ];
        return view('admin.dashboard',['counts' => $counts]);
    }
}
