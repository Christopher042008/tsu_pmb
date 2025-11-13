<?php

namespace App\Models\User;

use App\Models\MasterData\Master_Soal;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class JawabanTest extends Model
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'pmb_jawaban_peserta';
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
    public function soaltest(){
        return $this->hasOne('App\Models\MasterData\Master_Soal', 'kode_soal','kodesoal');
    }
}
