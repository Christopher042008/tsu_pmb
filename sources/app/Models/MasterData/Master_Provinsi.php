<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Master_Provinsi extends Model
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'pmb_master_provinsi';
    protected $primaryKey = 'idprov';
    protected $fillable = [
    //     'role_access',
    //     'password',
    //     'created_at',
    //     'created_by',
    //     'updated_at',
    //     'updated_by',
    ];

    public function kabupaten(){
        return $this->hasMany('App\Models\MasterData\Master_Kabupaten', 'idprov','idprov')->where('isactive',1);
    }
}
