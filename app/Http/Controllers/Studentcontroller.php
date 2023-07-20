<?php

namespace App\Http\Controllers;
use App\Models\Student;
use Auth;
use Hash;
use DateTime;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;


class Studentcontroller extends Controller
{
    public function studentregister(Request $request){
  
        $validator= Validator::make($request->all(),
        [
         
          'name'=>'required',
           'email'=>'required|email|unique:students,email',
          'password'=>'required|confirmed|min:6|max:6',
           'image'=>'image|mimes:jpeg,jpg,png,gif,svg|max:2048',
           'dob' => 'required|date_format:m-d-Y,d-m-Y,Y-m-d',
        ]);
      if($validator->fails()){
          return response()->json([$validator->errors()],400);
      }
      
      $user = new Student;
      $user->name=$request->name; 
      $user->class_id=$request->class_id; 
      $user->email=$request->email; 
      $user->mobile=$request->mobile; 

      $formats = ['m-d-Y', 'd-m-Y', 'Y-m-d'];
      $dob = null;
      foreach ($formats as $format) {
          $parsedDate = Carbon::createFromFormat($format, $request->dob);
          if ($parsedDate && $parsedDate->format($format) === $request->dob) {
              $dob = $parsedDate->format('Y-m-d');
              break;
          }
      }
      $user->dob=$dob;
      $user->address=$request->address;
      $user->class_id=$request->class_id; 
      $user->password=$request->password; 
      if($request->hasfile('image'))
      {
      $image = $request->file('image');
      $extension = $image->getClientOriginalExtension();
      $pathname = time().".".$extension;
      $image->move(public_path('student'),$pathname);
      $user->image = $pathname;
      }
      $user->save();
   

      if ($user->image) {
        $imageUrl = asset('student/' . $user->image);
        $user->image = $imageUrl;
      }
      return  response()->json(["data"=>$user,'staus'=>'true',"message"=>" student register successs"]);
       }
      public function studentlogin(Request $request){
      
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6|max:255', // Adjust the max length as needed
            ]);
    
            // Check if validation fails and return errors if any
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
    
            // Find the user by email
            $email = $request->input('email');
            $user = Student::where('email', $email)->first();
    
            // If the user is not found, return an error response
            if (!$user) {
                return response()->json(['status' => 'false', 'error' => 'Check your email'], 401);
            }
    
            // Check if the provided password matches the hashed password
            if (!password_verify($request->input('password'), $user->password)) {
                return response()->json(['message' => 'Password is incorrect'], 404);
            }
    
            // Attempt to authenticate the user
            $credentials = $request->only('email', 'password');
            if (!$token = Auth::attempt($credentials)) {
                return response()->json(['status' => 'false', 'error' => 'You are not registered or check your email and password'], 401);
            }
    
            // Authentication successful, create and return a new token along with user details
            return $this->createnewtokenstudent($token);
        }
    
        public function createnewtokenstudent($token)
        {
            $user = Auth::user();
            
            if ($user->image) {
                $imageUrl = asset('student/' . $user->image);
                $user->image = $imageUrl;
            }
    
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 7200, // Time in minutes (adjust as needed)
                'user' => $user,
                'status' => 'true',
                'message' => 'Student login success',
            ]);
        
    }
      
      public function student_update(Request $request )
      {

        $user = JWTAuth::parseToken()->authenticate();
$id = $user->id;

$validator = Validator::make($request->all(), [
    'email' => 'email|unique:students,email,' . $id,
    'password' => 'confirmed|min:6|max:6',
    'image' => 'image|mimes:jpeg,jpg,png,gif,svg|max:2048',
    'dob' => 'date_format:m-d-Y,d-m-Y,Y-m-d',
]);

if ($validator->fails()) {
    return response()->json(['status' => 'false', 'errors' => $validator->errors()], 400);
}

$user = Student::find($id);
if (!$user) {
    return response()->json(['status' => 'false', 'message' => 'Data not found']);
}

if (!is_null($request->name)) {
    $user->name = $request->name;
}

if (!is_null($request->class_id)) {
    $user->class_id = $request->class_id;
}

if (!is_null($request->email)) {
    $user->email = $request->email;
}

if (!is_null($request->mobile)) {
    $user->mobile = $request->mobile;
}

if (!is_null($request->address)) {
    $user->address = $request->address;
}

if (!is_null($request->file('Image'))) {
    $image = $request->file('Image');
    $extension = $image->getClientOriginalExtension();
    $pathname = time() . '.' . $extension;
    $image->move(public_path('student'), $pathname);
    $user->Image = $pathname;
}

if (!is_null($request->dob)) {
    $formats = ['m-d-Y', 'd-m-Y', 'Y-m-d'];
    $dob = null;
    foreach ($formats as $format) {
        $parsedDate = DateTime::createFromFormat($format, $request->dob);
        if ($parsedDate && $parsedDate->format($format) === $request->dob) {
            $dob = Carbon::parse($parsedDate)->toDateString();
            break;
        }
    }
    if (is_null($dob)) {
        return response()->json(['status' => 'false', 'message' => 'Invalid date format'], 400);
    }
    $user->dob = $dob;
}

$user->update();

if ($user->Image) {
    $imageUrl = asset('student/' . $user->Image);
    $user->Image = $imageUrl;
}

return response()->json(["data" => $user, 'status' => 'true', "message" => "Student update successful"]);
      }
      public function student_show()
      {
        $user = JWTAuth::parseToken()->authenticate();
        $id=$user->id;
        $student = Student::with('family','subject','marks' )->find($id);
        if(!$student){
          return response()->json(["status"=>"false", "message" => "student data not found"]);
        }
      return response()->json(["data"=>$student,"message" => "student data show"]);
      }
      public function Resetpassword(Request $request)
    {

        $Validator=Validator::make($request->all(),[
            'old_password' => 'required|min:6|max:6',
            'newpassword' => 'confirmed|min:6|max:6',
                
        ]);
        if ($Validator->fails()) {
            return response()->json([$Validator->errors()], 400);
        }
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
    }

    $id = $user->id;
    

    $student = Student::find($id); 
    if(!$student){
            return response()->json(['status'=>'false' ,'message'=>'id not found'],400);
        }

        if (password_verify($request->old_password, $student->password)) {

    $student->password = bcrypt($request->newpassword);
    $student->save();
    return response()->json(['message' => 'password changed']);
} else {
    return response()->json(['message' => 'old password does not match'], 400);
}
    }
}
