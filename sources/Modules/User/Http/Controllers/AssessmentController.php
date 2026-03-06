<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Batch;
use App\Models\MasterData\Master_JenisBerkas;
use App\Models\Parameter;
use App\Models\User\Biodata;
use App\Models\User\Pendaftaran;
use App\Models\User\Saudara;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Session, Crypt, DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\masterdata\JenisBerkasController;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = array(
            'title' => 'Assessment Test',
            'menu' => 'Assessment Test'
        );

        return view('user::user.assessment.index',$data);
    }
}
