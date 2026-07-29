<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;

    protected $table = 'user';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'email',
        'password',
        'nama_lengkap',
        'id_role',
        'id_pasar',
        'status_akun',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    public function pasar()
    {
        return $this->belongsTo(Pasar::class, 'id_pasar');
    }
}