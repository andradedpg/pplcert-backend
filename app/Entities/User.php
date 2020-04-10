<?php

namespace App\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $hasher;
    protected $table = 'users';

    protected $fillable = [
        'id',
        'status',
        'name',
        'email',
        'cellphone',
        'description',
        'verification_level',
        'verified',
        'number_of_reviews'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function findForPassport($identifier)
    {
        return $this->orWhere('email', $identifier)->first();
    }

    public function validateForPassportPasswordGrant($passpharse)
    {   
        return hash('sha256', $passpharse) === $this->password;
    }

    public function OauthAcessToken()
    {    
        return $this->hasMany('App\Entities\OauthAccessToken', 'user_id', 'id');
    }
}
