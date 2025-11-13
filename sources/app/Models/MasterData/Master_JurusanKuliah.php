<?php

namespace App\Models\MasterData;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Master_JurusanKuliah extends Model
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'pmb_master_jurusankuliah';
    protected $primaryKey = 'id';
    // protected $fillable = [
    //     'nik',
    //     'role_access',
    //     'password',
    //     'created_at',
    //     'created_by',
    //     'updated_at',
    //     'updated_by',
    // ];

    public function jenjang(){
        return $this->hasOne('App\Models\MasterData\Master_Jenjang', 'id','idjenjang')->where('isactive','1');
    }

    public function JurusanSekolah(){
        return $this->hasOne('App\Models\MasterData\Master_JurusanSekolah', 'id','idjurusansekolah')->where('isactive','1');
    }

    public function Fakultas(){
        return $this->hasOne('App\Models\MasterData\Master_Fakultas', 'KodeFakultas','idfakultas')->where('isactive','1');
    }
}
