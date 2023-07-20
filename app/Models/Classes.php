<?php

namespace App\Models;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Marks;
use App\Models\Totals;
use App\Models\Classes;
use App\Models\studentfamilyes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }
    public function subject(){
        return $this->hasmany(Subject::class,'class_id');
       }
       public function staff(){
        return $this->hasmany(Staff::class,'class_id');
       }
       public function student(){
        return $this->hasmany(Student::class,'class_id');
       }
    //    public function totals()
    //    {
    //        return $this->hasManyThrough(Totals::class, Student::class, 'class_id', 'student_id');
    //    }
    
    //    public function studentfamilyes(){
        
    //     return $this->belongsToMany(studentfamilyes::class,'subjects','class_id','id',);
    //    }
       public function marks()
       {
           return $this->hasManyThrough(Marks::class, Subject::class, 'class_id', 'subject_id');
       }

}
