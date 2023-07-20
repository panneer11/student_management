<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staffcontroller;
use App\Http\Controllers\Classcontroller;
use App\Http\Controllers\Studentcontroller;
use App\Http\Controllers\Markcontroller;
use App\Http\Controllers\Studentfamilycontroller;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//........Staffcontroller....routes......//
//manikandan code
Route::group(['prefix' => 'auth','middleware' => ['assign.guard:staffs']],function (){
    Route::post('staff_register',[Staffcontroller::class,'staffregister']);
    Route::post('staff_login',[Staffcontroller::class,'stafflogin']);
    Route::group(['middleware' => ['Studentmanagementmiddleware']],function (){
    Route::put('Resetstaffpassword',[Staffcontroller ::class,'Resetstaffpassword']);
    Route::post('staff_update',[Staffcontroller::class,'staff_update']);
    Route::get('class_show',[Classcontroller::class,'class_show']);
    });
});
//........classcontroller....routes......//
Route::post('class_insert',[Classcontroller::class,'class_insert']);
Route::put('class_update',[Classcontroller::class,'class_update']);


//........markcontroller....routes......//
Route::post('mark_insert',[Markcontroller::class,'mark_insert']);
Route::put('mark_update',[Markcontroller::class,'mark_update']);



//........studentcontroller....routes......//
Route::group(['prefix' => 'auth','middleware' => ['assign.guard:students',]],function (){
    Route::post('student_register',[Studentcontroller::class,'studentregister']);
    Route::post('student_login',[Studentcontroller::class,'studentlogin']);
    Route::group(['middleware' => ['Studentmanagementmiddleware']],function (){

        Route::post('student_update',[Studentcontroller::class,'student_update']);
        Route::post('student_familyinsert',[Studentfamilycontroller ::class,'student_familyinsert']);
    Route::put('student_familyupdate',[Studentfamilycontroller ::class,'student_familyupdate']);
    Route::put('Resetpassword',[Studentcontroller ::class,'Resetpassword']);
    Route::get('student_show',[Studentcontroller::class,'student_show']);
        });
   
   
    
    
});


