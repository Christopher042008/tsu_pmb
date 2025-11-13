<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Transaksi extends Model
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    // protected $keyType = 'string';
    protected $fillable = [
        'id',
    //     'role_access',
    //     'password',
    //     'created_at',
    //     'created_by',
    //     'updated_at',
    //     'updated_by',
    ];

    public function history_transaksi()
    {
        return $this->hasMany('App\Models\TransaksiHistory', 'transaksi_id','id');
    }

    public function pendaftaran()
    {
        return $this->hasOne('App\Models\User\Pendaftaran', 'KodePendaftaran','id_referensi');
    }

    public function biodata()
    {
        return $this->hasOne('App\Models\User\Biodata', 'biodata_id','user_id');
    }
}
