<?php

namespace Modules\Admin\Http\Controllers\masterdata;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Session, Crypt, DB;

class ContentController extends Controller
{
    public function index(){

        $data = array(
            'title' => 'Master Content PMB',
            'menu'  => 'Content PMB',
        );
        return view('admin::masterdata.content.index', $data);
    }
}
