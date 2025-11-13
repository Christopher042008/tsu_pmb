<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Master_Kecamatan extends Model
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'pmb_master_kecamatan';
    protected $primaryKey = 'id';
    protected $fillable = [
    //     'role_access',
    //     'password',
    //     'created_at',
    //     'created_by',
    //     'updated_at',
    //     'updated_by',
    ];

    public function provinsi(){
        return $this->hasOne('App\Models\MasterData\Master_Provinsi', 'idprov','idprov')->where('isactive',1);
    }


}
