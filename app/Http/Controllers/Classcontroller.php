<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Classes;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class Classcontroller extends Controller
{
    public function class_insert(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|unique:classes,name',
        'sub_name' => 'required|array|min:1',
        'sub_name.*' => 'required|string',
    ]);
    if ($validator->fails()) {
        return response()->json(['status' => 'false','errors' =>$validator->errors()], 400);
    }
    $class = new classes;
    $class->name = $request->name;
    $class->save();
    $id = $class->id;
    $sub_names = $request->sub_name;
    foreach ($sub_names as $sub_name) {
        $subject = new Subject;
        $subject->class_id = $id;
        $subject->sub_name = $sub_name;
        $subject->save();
    }
    $class = classes::with('subjects')->find($id);
    return response()->json(['data' => $class, 'status' => 'true', 'message' => 'Data inserted successfully'], 200);
      }
      public function class_update(Request $request){
        $data = $request->all();
$class_id = $data['class'][0]['id'];
        $validator = Validator::make($request->all(), [
            'class.*.name' => 'required|unique:classes,name,' . $class_id,
            'records' => 'required|array|min:1',
            'records.*.sub_name' => 'required|string',
            'records.*.id' => 'nullable|exists:subjects,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => 'false', 'errors' => $validator->errors()], 400);
        }
        $class = Classes::find($class_id);
        
        if (!$class) {
            return response()->json(['status' => 'false', 'message' => 'Class not found'], 404);
        }
        
        $class->name = $request->class[0]['name'];
        $class->save();
    
        $records = $request->records;
        
        foreach ($records as $record) {
            if (isset($record['id'])) {
                $subject = Subject::find($record['id']);
            } else {
                $subject = new Subject;
                $subject->class_id = $class_id;
            }
        
            $subject->sub_name = $record['sub_name'];
            $subject->save();
        }
        
        $class = Classes::with('subjects')->find($class_id);
        
        return response()->json(['data' => $class, 'status' => 'true', 'message' => 'Data updated successfully'], 200);    
      
}
public function class_show()
{
    $user = JWTAuth::parseToken()->authenticate();
    $id=$user->class_id;
    $class = Classes::with( 'staff','subject', 'student', 'marks')->find($id);

    if ($class) {
        $students = $class->student;
        $studentNames = $students->pluck('name')->toArray();
        $subject = $class->subject;
        $staff = $class->staff;
        $studentData = [];

        foreach ($students as $student) {
            $studentId = $student->id;
            $marks = $student->marks;
            $markValues = $marks->pluck('mark')->toArray();
            $total = array_sum($markValues);
            $studentData[] = [
                'student_id' => $studentId,
                'marks' => $markValues,
                'total' => $total,
            ];
        }

        // Sort the studentData array based on the total marks in descending order
        usort($studentData, function ($a, $b) {
            return $b['total'] - $a['total'];
        });

        $rankedStudents = [];
        $rank = 1;
        foreach ($studentData as $student) {
            $studentId = $student['student_id'];
            $rankedStudents[] = [
                'rank' => $rank . 'rank',
                'student_id' => $studentId,
            ];
            $rank++;
        }
        return response()->json( [ "staffs"=>$staff,"total students"=>["student_name"=>$studentNames,"subjects"=>$subject,"student marks"=> $studentData], "student rank"=>$rankedStudents]);
    } else {
        return response()->json(['message'=>'Class not found.']);
    }
}

}
