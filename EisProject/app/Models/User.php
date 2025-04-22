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
    use HasApiTokens, HasFactory, Notifiable;

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
        'year_started',
        'aptis_level',
        'initial_bourse_percentage',
        'current_bourse_percentage',
        'verification_code',
        'supervised_id',
        'password',
        'role_id',
        'department_id',
        'degree_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key-value array of custom claims.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(Departments::class, 'department_id', 'id');
    }
    public function degree()
    {
        return $this->belongsTo(Degree::class, 'degree_id', 'id');
    }
    public function courses()
    {
        return $this->belongsToMany(Course::class)->withPivot('role')->withTimestamps();
    }
    public function lecturersCourses()
    {

        return $this->courses()->wherePivot('role', 'lecturer');
    }
    public function assistantsCourses()
    {
        return $this->courses()->wherePivot('role', 'assistant');
    }
    public function studentsCourses()
    {
        return $this->courses()->wherePivot('role', 'student');
    }

    public function receivedGrades()
    {
        return $this->hasMany(Grade::class, 'student_id');
    }
    public function givenGrades()
    {
        return $this->hasMany(Grade::class, 'lecture_id');
    }
    public function sutendGroup()
    {
        return $this->classGroup()->wherePivot('role', 'student');

    }
    public function payment()
    {
        return $this->hasMany(Payment::class);
    }
    public function documentRequest()
    {
        return $this->hasMany(DocumentRequest::class, 'student_id', 'id');
    }
}