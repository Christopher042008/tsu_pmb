<?php

namespace App\Models\MasterData;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Master_Beasiswa extends Model
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'pmb_master_beasiswa';
    protected $primaryKey = 'id';
    // protected $keyType = 'string';
    protected $fillable = [
        'IdJalur ',
        'idtingkat ',
    //     'role_access',
    //     'password',
    //     'created_at',
    //     'created_by',
    //     'updated_at',
    //     'updated_by',
    ];
    public function jalur(){
        return $this->hasOne('App\Models\MasterData\Master_JenisPendaftaran', 'id','IdJalur')->where('isactive',1);
    }
    public function tingkat(){
        return $this->hasOne('App\Models\MasterData\Master_Tingkat', 'id','idtingkat')->where('isactive',1);
    }
}
