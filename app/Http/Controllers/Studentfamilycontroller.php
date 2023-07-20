<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\studentfamilyes;
use App\Models\Student;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use Illuminate\Support\Facades\DB;
class Studentfamilycontroller extends Controller
{
  public function student_familyinsert(Request $request)
  {
    $user = JWTAuth::parseToken()->authenticate();
$id = $user->id;

$validator = Validator::make($request->all(), [
    '*.name' => 'required',
    '*.relation' => 'required',
    '*.qualification' => 'required',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 400);
}
$insertedData = [];
foreach ($request->all() as $item) {
    // $student_id = Studentfamilyes::where('student_id', $id)->first();
    // if ($student_id) {
    //     return response()->json(['error' => 'Student ID already exists'], 400);
    // }

    $studentFamily = new Studentfamilyes;
    $studentFamily->name = $item['name'];
    $studentFamily->relation = $item['relation'];
    $studentFamily->qualification = $item['qualification'];
    $studentFamily->student_id = $id;
    $studentFamily->save();
    $insertedData[] = [
      'id' => $studentFamily->id,
      'name' => $item['name'],
      'relation' => $item['relation'],
      'qualification' => $item['qualification'],
  ];
}

return response()->json([
  'status' => 'true',
  'message' => 'Data inserted successfully',
  'data' => [
      'student_id' => $id,
      'marks' => $insertedData,
  ],
], 200);
  }
  
      public function student_familyupdate(Request $request)
      {
        
   $user = JWTAuth::parseToken()->authenticate();
    $id = $user->id;
    $marks = $request->input('familys');
    $updatedData = [];
    
    foreach ($marks as $mark) {
        $subjectId = $mark['id'];
        $name = $mark['name'];
        $relation = $mark['relation'];
        $qualification = $mark['qualification'];
        $subjectMark = Studentfamilyes::where('student_id', $id) ->where('id', $subjectId)->first();

        if ($subjectMark) {
            $subjectMark->name = $name;
            $subjectMark->relation = $relation;
            $subjectMark->qualification = $qualification;
            $subjectMark->save();

            $updatedData[] = [
                'id'=>$subjectMark->id,
                'name' => $name,
                'relation' => $relation,
                'qualification' => $qualification,
            
            ];
        }
    }

    return response()->json([
        'status' => 'true',
        'message' => 'Data updated successfully',
        'data' => [
            'student_id' => $id,
            'marks' => $updatedData,
        ],
    ], 200);
  
   
}   

}
