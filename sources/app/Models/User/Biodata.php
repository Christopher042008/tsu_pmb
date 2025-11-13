<?php

namespace App\Models\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Biodata extends Model
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'pmb_biodata';
    protected $primaryKey = 'biodata_id';
    // protected $fillable = [
    //     'nik',
    //     'role_access',
    //     'password',
    //     'created_at',
    //     'created_by',
    //     'updated_at',
    //     'updated_by',
    // ];

    public function akunbio(){
        return $this->belongsTo('App\Models\MasterData\Master_Akun', 'akun','akun_id')->where('isactive','1');
    }

    public function saudara()
    {
        return $this->hasMany('App\Models\User\Saudara', 'bio_id','biodata_id');
    }
}
