<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Helper\Ui\FlashMessageGenerator;

class FrontController extends Controller
{

    public function index(){

        $data= (object)[

        ];
        
        return view('Frontend.index', compact('data'));
    }

}
