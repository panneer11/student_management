<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\studentfamilyes;
use App\Models\Marks;
use App\Models\Subject;
class Student extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    public function family(){
        return $this->hasone(studentfamilyes::class);
       }
       public function marks(){
        return $this->hasmany(Marks::class,'student_id');
       }
       public function subject(){
        return $this->belongsToMany(Subject::class,'marks');
       }

    use HasFactory;
    
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

}
