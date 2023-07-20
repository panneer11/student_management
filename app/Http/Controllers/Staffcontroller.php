<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Staff;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Staffcontroller extends Controller
{

  public function index(Request $request){
    return 'staff';
  }
public function staffregister(Request $request){
  
    $validator= Validator::make($request->all(),
    [
      'name'=>'required',
       'email'=>'required|email|unique:staffs,email',
      'password'=>'required|confirmed|min:6|max:6',
       'image'=>'image|mimes:jpeg,jpg,png,gif,svg|max:2048'
    ]);
  if($validator->fails()){
      return response()->json([$validator->errors()],400);
  }
 
$user = new Staff;
$user->name=$request->name; 
$user->class_id=$request->class_id; 
$user->email=$request->email; 
 $user->password=$request->password; 

if($request->hasfile('Image'))
{
$image = $request->file('Image');
$extension = $image->getClientOriginalExtension();
$pathname = time().".".$extension;
$image->move(public_path('staff'),$pathname);
$user->Image = $pathname;
}
$user->save();
if ($user->Image) {
  $imageUrl = asset('staff/' . $user->Image);
  $user->Image = $imageUrl;
}
  return  response()->json(["data"=>$user,"status"=>'true',"message"=>" staff register successs"]);
}
public function stafflogin(Request $request){
  $validator= Validator::make($request->all(),
  [
    'email'=>'required|email',
    'password'=>'required|min:6|max:6',
  ]);
  if ($validator->fails()) {
    return response()->json(['status' => 'false','errors' => $validator->errors()], 422);
}
$email = $request->input('email');
$user = Staff::where('email', $email)->first();

if (!$user) {
    return response()->json(['status' => 'false', 'error' => 'Check your email'], 401);
}
if (!password_verify($request->input('password'), $user->password)) {
  return response()->json(['message' => 'Password is incorrect'], 404);
}


$credentials = request(['email', 'password']);
if (! $token = auth()->attempt($credentials)) {
    return response()->json(['status' => 'false','error' => 'you are not is register'], 401);
}
return $this->createnewtoken($token);

}
public function createnewtoken($token){
$user=auth()->user();
if ($user->Image) {
  $imageUrl = asset('staff/' . $user->Image);
  $user->Image = $imageUrl;
}

  return response()->json([
'access_token'=>$token,
'token_type'=>'bearer',
'expires_in'=>auth()->factory()->getTTL()*7200,
'user'=>$user,
"message"=>" staff login successs"
  ]);
}
public function staff_update(Request $request)
{

$user = JWTAuth::parseToken()->authenticate();
  $id=$user->id;
  $validator= Validator::make($request->all(),
    [
       'email'=>'email|unique:staffs,email,'.$id,
       'image'=>'image|mimes:jpeg,jpg,png,gif,svg|max:2048'
    ]);
  if($validator->fails()){
      return response()->json(['status' => 'false','errors' =>$validator->errors()],400);
  }
  $user=Staff::find($id);
  if(!$user)
     return  response()->json([ 'status' => 'false','message'=>' data not found']);
     if ($user) {

      if (!is_null($request->name)) {
          $user->name = $request->name;
      }
  
      if (!is_null($request->class_id)) {
          $user->class_id = $request->class_id;
      }
  
      if (!is_null($request->email)) {
          $user->email = $request->email;
      }
      if (!is_null($request->file('Image'))) {
        $image = $request->file('Image');
        $extension = $image->getClientOriginalExtension();
        $pathname = time() . '.' . $extension;
        $image->move(public_path('staff'), $pathname);
        $user->Image = $pathname;
    }
      $user->update();
  
  }
if ($user->Image) {
  $imageUrl = asset('staff/' . $user->Image);
  $user->Image = $imageUrl;
}
  return  response()->json(["data"=>$user,'status' => 'true',"message"=>"staff update successs"]);
}
public function Resetstaffpassword(Request $request)
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
    

    $student = Staff::find($id); 
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
