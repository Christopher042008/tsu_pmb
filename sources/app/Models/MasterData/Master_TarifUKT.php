<?php

namespace App\Models\MasterData;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Master_TarifUKT extends Model
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'pmb_master_tarifukt';
    protected $primaryKey = 'id';
    protected $fillable = [
        'idbatch',
        'idjalur',
        'idjurusan',
    ];

    public function batch(){
        return $this->hasOne('App\Models\MasterData\Master_Batch', 'id','idbatch')->where('isactive',1);
    }
    public function jalur(){
        return $this->hasOne('App\Models\MasterData\Master_JenisPendaftaran', 'id','idjalur')->where('isactive',1);
    }
    public function jurusan(){
        return $this->hasOne('App\Models\MasterData\Master_JurusanKuliah', 'id','idjurusan')->where('isactive',1);
    }
}
