<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

   /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'institution_number',
        'id_cart_number',
        'name',
        'surname',
        'email',
        'secondary_email',
        'birthdate',
        'nationality',
        'gender',
        'blood_group',
        'civil_status',
        'years_sarted',
        'aptis_level',
        'bourse_percantage',
        'verification_code',
        'supervised_id',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * Get the role that owns the user.
     */ 
    public function role(){
        return $this->hasMany(Role::class);
    }
    /**
     * Get the degree that owns the user.
     */
    public function degree(){
        return $this->hasMany(Degree::class);   
}
    public function groupClass(){
        return $this->hasMany(GroupClass::class);
    }
    public function department(){
        return $this->hasMany(Departments::class);
    }
   
}
