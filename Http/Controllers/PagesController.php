<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PagesController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth','showMethods']);
    }

    public function index()
    {
        $data = [
            'header_title' => 'Dashboard'
        ];
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        return view('dashboard')->with($data);
    }

    public function login(){
        if (Auth::check()) {
            return $this->index();
        }

        return view('auth.login');
    }
}
