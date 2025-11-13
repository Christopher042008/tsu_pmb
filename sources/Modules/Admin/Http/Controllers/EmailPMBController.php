<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Session, Crypt, DB;

class EmailPMBController extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'Email PMB',
            'menu'  => 'Email PMB',
        );
        return view('admin::emailPMB.index', $data);
    }
}
