<?php

namespace App\Auth\Models;

use App\Core\Dao\SDB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;
    protected $table = 'dtb_customer';//may be change table name
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'customer_id';
    protected $fillable = [
        'name', 'email', 'password','role_value'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'secret_key',
    ];
    public function details() {
        return SDB::table('users_detail')->where(array('id',$this->getKey())) ;
    }
}
