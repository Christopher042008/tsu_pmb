<?php

namespace App\Models\MasterData;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Master_Berkas extends Model
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'pmb_master_berkas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'KodeBerkas',
        'IdJenis',
    //     'password',
    //     'created_at',
    //     'created_by',
    //     'updated_at',
    //     'updated_by',
    ];

    public function jenis(){
        return $this->hasOne('App\Models\MasterData\Master_JenisBerkas', 'id','IdJenis')->where('isactive','1');
    }
}
