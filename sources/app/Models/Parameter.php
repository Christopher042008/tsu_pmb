<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Parameter extends Model
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'parameter';
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
}
