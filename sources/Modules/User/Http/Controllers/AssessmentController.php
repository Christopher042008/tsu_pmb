<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Assessment_Attempts;
use App\Models\Assessment\Assessment_TipeTest;
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
        $bioId = decrypt(session('user')->_biodata);
        $status = Pendaftaran::join('pmb_master_batchpendaftaran as a','pmb_pendaftaran.batch_daftar','=','a.id')
        ->where('pmb_pendaftaran.biodata_id',$bioId)
        ->whereRaw('? BETWEEN a.tglmulai and a.tglselesai',[date('Y-m-d')])
        // ->whereRaw('(bayar_pendaftaran="1" OR bayar_pendaftaran="-1")')
        ->with('batch')
        ->selectRaw('pmb_pendaftaran.*')
        ->first();
        $tests = Assessment_TipeTest::where('isactive',1)
        ->orderBy('urutan')
        ->get();
        $attempts = Assessment_Attempts::where('kodependaftaran', $bioId)
        ->get()
        ->keyBy('tipe_test_id');
        $data = array(
            'title' => 'Assessment Test',
            'menu' => 'Assessment Test',
            'data' => $status,
            'test' => $tests,
            'attempts' => $attempts
        );
        return view('user::user.assessment.index',$data);
    }
}
