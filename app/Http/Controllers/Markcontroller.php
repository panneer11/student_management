<?php

namespace App\Http\Controllers;
use App\Models\Marks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class Markcontroller extends Controller
{
    public function mark_insert(Request $request)
  {
  //panneer
    
        $studentId = $request->input('student_id');
        $validator = Validator::make($request->all(), [
            'student_id' => 'unique:marks,student_id,' . $studentId,
            'marks.*.subject_id' => 'required|integer',
            'marks.*.mark' => 'required|integer|min:0|max:100',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }
    
        $marks = $request->input('marks');
        $insertedData = [];
    
        foreach ($maurks as $mark) {
            $subjectMark = new Marks;
            $subjectMark->student_id = $studentId;
            $subjectMark->subject_id = $mark['subject_id'];
            $subjectMark->mark = $mark['mark'];
            $subjectMark->save();
    
            $insertedData[] = [
                'subject_id' => $mark['subject_id'],
                'mark' => $mark['mark'],
            ];
        }
    
        return response()->json([
            'status' => 'true',
            'message' => 'Data inserted successfully',
            'data' => [
                'student_id' => $studentId,
                'marks' => $insertedData,
            ],
        ], 200);
    }
public function mark_update(Request $request)
{
  $studentId = $request->input('student_id');
    $validator = Validator::make($request->all(), [
        'student_id' => 'exists:marks,student_id',
        'marks.*.subject_id' => 'required|integer',
        'marks.*.mark' => 'required|integer|min:0|max:100',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'false',
            'message' => 'Validation Error',
            'errors' => $validator->errors(),
        ], 400);
    }

    $marks = $request->input('marks');
    $updatedData = [];

    foreach ($marks as $mark) {
        $subjectId = $mark['subject_id'];
        $newMark = $mark['mark'];

        $subjectMark = Marks::where('student_id', $studentId)
                            ->where('subject_id', $subjectId)
                            ->first();

        if ($subjectMark) {
            $subjectMark->mark = $newMark;
            $subjectMark->save();

            $updatedData[] = [
                'subject_id' => $subjectId,
                'mark' => $newMark,
            ];
        }
    }

    return response()->json([
        'status' => 'true',
        'message' => 'Data updated successfully',
        'data' => [
            'student_id' => $studentId,
            'marks' => $updatedData,
        ],
    ], 200);
}
}
