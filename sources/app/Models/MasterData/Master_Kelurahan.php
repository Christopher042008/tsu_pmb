<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Master_Kelurahan extends Model
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'pmb_master_kelurahan';
    protected $primaryKey = 'id';
    protected $fillable = [
    //     'role_access',
    //     'password',
    //     'created_at',
    //     'created_by',
    //     'updated_at',
    //     'updated_by',
    ];


}
