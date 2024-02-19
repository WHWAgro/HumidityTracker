<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Fields;
use App\Models\Pin;
use App\Models\PinHumidity;
use App\Models\Resume;
use Auth;
use Mail;
use App\Mail\VerificationMail;
use Hash;
use DB;

date_default_timezone_set('America/Santiago');

class ApiController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required:min:8'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        // $input = $request->all();
        // $input['password'] = bcrypt($input['password']);
        $input['otp'] = rand(100000,999999);
        $user = DB::table('temporary_users')
                    ->where('email',$request->email)
                    ->where('password',$request->password)
                    ->where('is_used',0);

        $details = [
            'verification_code' => $input['otp']
        ];

        if($user->count() > 0)
            $user = $user->update(['otp'=>$input['otp']]);
        else
            return $this->sendError('Invalid email', 'Please use valid email');       

        Mail::to($request->email)->send(new VerificationMail($details));
        // $success['token'] =  $user->createToken('WHW')->accessToken;
        $success['otp'] = (string)$input['otp'];
        $success['email'] = $request->email;
        return $this->sendResponse($success, 'Verification mail has been sent to your mail.');
    }

    public function login(Request $request)
    {   
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required:min:8',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('WHW')-> accessToken; 
            $success['user_id'] = Auth::user()->id;
            // dd($accessToken);
            return $this->sendResponse($success, 'Logged in successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised', ['error'=>'Unauthorised']);
        } 
    }

    public function resend_code(Request $request)
    {
        $email = $request->email;
        $type = $request->type;
        $verification_code = rand(100000,999999);

        if($type == 'create')
        {
            $check = DB::table('temporary_users')->where('email',$email)->where('is_used','0')->count();
            if($check == 1)
                $data = DB::table('temporary_users')->where('email',$email)->update(['otp'=>$verification_code]);
            else
                return $this->sendError('Unauthorised', ['error'=>'Please enter valid email']);
        }
        if($type == 'forgot')
        {
            $check = User::where('email',$email)->count();
            if($check == 1)
                $data = User::where('email',$email)->update(['otp'=>$verification_code]);
            else
                return $this->sendError('Unauthorised', ['error'=>'Please enter valid email']);   
        }

        $details = [
            'verification_code' => $verification_code
        ];
        
        Mail::to($email)->send(new VerificationMail($details));
        $success['otp'] = (string)$verification_code;
        $success['email'] = $request->email;
        return $this->sendResponse($success, 'Verification mail has been sent to your mail.');
    }


    public function verify_otp(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email' => 'required|email',
            'verification_code' => 'required',
            'type' => 'required'
        ]);

        if($validate->fails()){
            return $this->sendError('Validation Error.', $validate->errors());       
        }

        $ver_code = $request->verification_code;
        $email = $request->email;
        $type = $request->type;

        if($type == 'create')
        {
            $check = DB::table('temporary_users')->where([
                'email' => $email,
                'otp' => $ver_code
            ])->get();
        }    
        if($type == 'forgot')
        {
            $check = User::where([
                        'email' => $email,
                        'otp' => $ver_code
                    ])->get();
        }

        if(count($check) == 1)
        {
            if($type == 'create')
            {
                DB::table('temporary_users')->where([
                    'email' => $email
                ])->update(['otp'=>null]);
            }
            if($type == 'forgot')
            {
                User::where([
                        'email' => $email
                    ])->update(['otp'=>null]);
            }

            $success['status'] = 'Verified';
            $success['email'] = $email;

            return $this->sendResponse($success, 'Verification Code Verified.');
        }

         return $this->sendError('Unverified', ['error'=>'Verification code wrong']);
    }

    public function create_password(Request $request)
    {
        if($request->all())
        {
            $validate = Validator::make($request->all(),[
                'password' => 'required|min:8',
                'confirm_password' => 'required|same:password',
                'email'=> 'required',
                'type' => 'required'
            ]);

            if($validate->fails()){
                return $this->sendError('Validation Error.', $validate->errors());       
            }

            $password = $request->password;
            $password = Hash::make($password);
            $email = $request->email;
            $type = $request->type;

            if($type == 'create')
            {
                $data = User::create([
                    'email' => $email,
                    'password' => $password
                ]);

                $update_temporary = DB::table('temporary_users')
                                        ->where('email',$email)
                                        ->update(['is_used' => '1']);

                                        
            }
            if($type == 'forgot')
            {
                $data = User::where('email',$email)->update([
                    'password' => $password
                ]);

            }

            $user_id = User::select('id')->where('email',$request->email)->first();
            $user = Auth::loginUsingId($user_id->id);
            $success['token'] =  $user->createToken('WHW')-> accessToken; 
            $success['user_id'] = $user_id->id;

            return $this->sendResponse($success, 'Password Created Successfully.');
        }
        
        return $this->sendError('undefined', ['error'=>'Something went wrong']);
    }

    public function forgot_password(Request $request)
    {
        if($request->email)
        {
            $validate = Validator::make($request->all(),[
                'email' => 'required|email',
                'type' => 'required'
            ]);

            if($validate->fails()){
                return $this->sendError('Validation Error.', $validate->errors());       
            }
            
            $email = $request->email;
            $type = $request->type;

            if($type == 'create')
            {
                $check = DB::table('temporary_users')->where([
                    'email' => $email
                ])->get();
            }    
            if($type == 'forgot')
            {
                $check = User::where([
                            'email' => $email
                        ])->get();
            }

            if(count($check) == 1)
            {
                $verification_code = rand(100000,999999);

                if($type == 'create')
                {
                   $data = DB::table('temporary_users')->where([
                        'email' => $email
                    ])->update(['otp'=>$verification_code]);
                }
                if($type == 'forgot')
                {
                   $data = User::where([
                            'email' => $email
                        ])->update(['otp'=>$verification_code]);
                }

                $details = [
                    'verification_code' => $verification_code
                ];
                
                Mail::to($email)->send(new VerificationMail($details));

                $success['otp'] = (string)$verification_code;
                $success['email'] = $email;

                return $this->sendResponse($success, 'Verification mail has been sent to your mail.');
            }
            else
                return $this->sendError('Not Registered', ['error'=>'This E-mail is not Registered']);
        }
    }

    // public function track_humidity(Request $request)
    // {
    //     $keyword = $request->search;
    //     $field_data = Fields::join('pins','fields.id','pins.field_id')
    //                         ->join('pin_humidities','pins.id','pin_humidities.pin_id')
    //                         ->whereDate('pins.humidity_date',date('Y-m-d'))
    //                         ->where(function($query) use ($keyword){
    //                             $query->where('fields.field_code','like','%'.$keyword.'%')
    //                                     ->orWhere('fields.field_name','like','%'.$keyword.'%');
    //                             })
    //                         ->get();
        
    //     $field_names = Fields::select('id','field_code','field_name','map_file')
    //                         ->where('field_code','like','%'.$request->search.'%')
    //                         ->orWhere('field_name','like','%'.$request->search.'%')
    //                         ->get();
        
    //     $new_array = [];
    //     foreach($field_data as $data) {
    //         if(!isset($new_array[$data["pin_id"]])) {
    //             $new_array[$data["pin_id"]]["field_name"] = $data["field_name"];
    //             $new_array[$data["pin_id"]]["field_code"] = $data["field_code"];
    //             $new_array[$data["pin_id"]]["field_alias"] = $data["field_alias"];
    //             $new_array[$data["pin_id"]]["razon_social"] = $data["razon_social"];
    //             $new_array[$data["pin_id"]]["map_file"] = $data["map_file"];
    //             $new_array[$data["pin_id"]]["created_at"] = $data["created_at"];
    //             $new_array[$data["pin_id"]]["updated_at"] = $data["updated_at"];
    //             $new_array[$data["pin_id"]]["user_id"] = $data["user_id"];
    //             $new_array[$data["pin_id"]]["field_id"] = $data["field_id"];
    //             $new_array[$data["pin_id"]]["latitude"] = $data["latitude"];
    //             $new_array[$data["pin_id"]]["longitude"] = $data["longitude"];
    //             $new_array[$data["pin_id"]]["humidity_date"] = $data["humidity_date"];
    //             $new_array[$data["pin_id"]]["pin_id"] = $data["pin_id"];
    //             $new_array[$data["pin_id"]]["humidity"] = $data["humidity"];
    //         } else {
    //             $new_array[$data["pin_id"]]["field_name"] = $data["field_name"];
    //             $new_array[$data["pin_id"]]["field_code"] = $data["field_code"];
    //             $new_array[$data["pin_id"]]["field_alias"] = $data["field_alias"];
    //             $new_array[$data["pin_id"]]["razon_social"] = $data["razon_social"];
    //             $new_array[$data["pin_id"]]["map_file"] = $data["map_file"];
    //             $new_array[$data["pin_id"]]["created_at"] = $data["created_at"];
    //             $new_array[$data["pin_id"]]["updated_at"] = $data["updated_at"];
    //             $new_array[$data["pin_id"]]["user_id"] = $data["user_id"];
    //             $new_array[$data["pin_id"]]["field_id"] = $data["field_id"];
    //             $new_array[$data["pin_id"]]["latitude"] = $data["latitude"];
    //             $new_array[$data["pin_id"]]["longitude"] = $data["longitude"];
    //             $new_array[$data["pin_id"]]["humidity_date"] = $data["humidity_date"];
    //             $new_array[$data["pin_id"]]["humidity"] .= ", " . $data["humidity"];
    //         }
    //     }
    
    //     $success['data'] = array_values($new_array);
    //     foreach($field_names as $field)
    //     {
    //         $success['data'][] = [
    //             'field_name'=> $field->field_name,
    //             'field_code'=> $field->field_code,
    //             'field_alias'=> '',
    //             'razon_social'=> '',
    //             'map_file'=> $field->map_file,
    //             'created_at'=> '',
    //             'updated_at'=> '',
    //             'user_id'=> 0,
    //             'field_id'=> $field->id,
    //             'latitude'=> '',
    //             'longitude'=> '',
    //             'humidity_date'=> '',
    //             'pin_id'=> 0,
    //             'humidity'=> '',
    //         ];
    //     }

    //     return $this->sendResponse($success, 'Search Results');
    // }

    public function track_humidity(Request $request)
    {
        $keyword = $request->search;
        $field_data = Fields::join('pins','fields.id','pins.field_id')
                            ->join('pin_humidities','pins.id','pin_humidities.pin_id')
                            ->whereDate('pins.humidity_date',date('Y-m-d'))
                            ->where(function($query) use ($keyword){
                                $query->where('fields.field_code','like','%'.$keyword.'%')
                                        ->orWhere('fields.field_name','like','%'.$keyword.'%');
                                })
                            ->get();
        
        $field_names = Fields::select('id','field_code','field_name','map_file')
                            ->where('field_code','like','%'.$request->search.'%')
                            ->orWhere('field_name','like','%'.$request->search.'%')
                            ->get();
        
        $new_array = [];
        foreach($field_data as $data) {
            if(!isset($new_array[$data["pin_id"]])) {
                $new_array[$data["pin_id"]]["field_name"] = $data["field_name"];
                $new_array[$data["pin_id"]]["field_code"] = $data["field_code"];
                $new_array[$data["pin_id"]]["field_alias"] = $data["field_alias"];
                $new_array[$data["pin_id"]]["razon_social"] = $data["razon_social"];
                $new_array[$data["pin_id"]]["map_file"] = $data["map_file"];
                $new_array[$data["pin_id"]]["created_at"] = $data["created_at"];
                $new_array[$data["pin_id"]]["updated_at"] = $data["updated_at"];
                $new_array[$data["pin_id"]]["user_id"] = $data["user_id"];
                $new_array[$data["pin_id"]]["field_id"] = $data["field_id"];
                $new_array[$data["pin_id"]]["latitude"] = $data["latitude"];
                $new_array[$data["pin_id"]]["longitude"] = $data["longitude"];
                $new_array[$data["pin_id"]]["humidity_date"] = $data["humidity_date"];
                $new_array[$data["pin_id"]]["pin_id"] = $data["pin_id"];
                $new_array[$data["pin_id"]]["humidity_data"][] = ['humidity_id' => $data["id"], 'humidity' => $data['humidity']];
            } else {
                $new_array[$data["pin_id"]]["field_name"] = $data["field_name"];
                $new_array[$data["pin_id"]]["field_code"] = $data["field_code"];
                $new_array[$data["pin_id"]]["field_alias"] = $data["field_alias"];
                $new_array[$data["pin_id"]]["razon_social"] = $data["razon_social"];
                $new_array[$data["pin_id"]]["map_file"] = $data["map_file"];
                $new_array[$data["pin_id"]]["created_at"] = $data["created_at"];
                $new_array[$data["pin_id"]]["updated_at"] = $data["updated_at"];
                $new_array[$data["pin_id"]]["user_id"] = $data["user_id"];
                $new_array[$data["pin_id"]]["field_id"] = $data["field_id"];
                $new_array[$data["pin_id"]]["latitude"] = $data["latitude"];
                $new_array[$data["pin_id"]]["longitude"] = $data["longitude"];
                $new_array[$data["pin_id"]]["humidity_date"] = $data["humidity_date"];
                $new_array[$data["pin_id"]]["humidity_data"][] = ['humidity_id' => $data["id"], 'humidity' => $data['humidity']];
            }
        }
    
        $success['data'] = array_values($new_array);
        foreach($field_names as $field)
        {
            $success['data'][] = [
                'field_name'=> $field->field_name,
                'field_code'=> $field->field_code,
                'field_alias'=> '',
                'razon_social'=> '',
                'map_file'=> $field->map_file,
                'created_at'=> '',
                'updated_at'=> '',
                'user_id'=> 0,
                'field_id'=> $field->id,
                'latitude'=> '',
                'longitude'=> '',
                'humidity_date'=> '',
                'pin_id'=> 0,
                'humidity_data'=> [],
            ];
        }

        return $this->sendResponse($success, 'Search Results');
    }

    public function track_humidity_android(Request $request)
    {
        $keyword = $request->search;

        $field_names = Fields::select('id','field_code','field_name','map_file')
                            ->where('field_code','like','%'.$request->search.'%')
                            ->orWhere('field_name','like','%'.$request->search.'%')
                            ->get();
        $success['data'] = [];
        
        foreach($field_names as $field)
        {
            $success['data'][] = [
                'field_name'=> $field->field_name,
                'field_code'=> $field->field_code,
                'field_alias'=> '',
                'razon_social'=> '',
                'map_file'=> $field->map_file,
                'created_at'=> '',
                'updated_at'=> '',
                'user_id'=> 0,
                'field_id'=> $field->id,
                'latitude'=> '',
                'longitude'=> '',
                'humidity_date'=> '',
                'pin_id'=> 0,
                'humidity_data'=> [],
            ];
        }

        return $this->sendResponse($success, 'Search Results');
    }

    public function add_humidity(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'user_id' => 'required|numeric',
            'field_id' => 'required|numeric',
            'latitude'=> 'required',
            'longitude'=> 'required',
            'humidity'=> 'required',
            'humidity_date'=> 'required',
        ]);

        if($validate->fails()){
            return $this->sendError('Validation Error.', $validate->errors());       
        }


        $user_id = $request->user_id;
        $field_id = $request->field_id;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $humidity = $request->humidity;
        $humidity_date = $request->humidity_date;
        
        $humidity_array = explode(",",$humidity[0]);
        $field_data_ids = [];
        $create = Pin::insertGetId([
            'user_id' => $user_id,
            'field_id' => $field_id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'humidity_date' => $humidity_date
        ]);
        foreach($humidity_array as $level)
        {   
            $add_humidity = PinHumidity::insertGetId([
                'pin_id'=>$create,
                'humidity'=>$level
            ]);
        }
        $field_data_ids['marker_id'] = $create;

        if($field_data_ids)
            return $this->sendResponse($field_data_ids, 'Humidity added successfully');
        else
            return $this->sendError('Error', ['error'=>'Humidity did not added.']);
    }

    public function edit_humidity(Request $request)
    {
        $humidity = $request->humidity;
        $humidity_id = $request->humidity_id;
        $pin_id = $request->pin_id;

        $humidity_array = explode(",",$humidity[0]);
        $humidity_id_array = explode(",",$humidity_id[0]);

        foreach($humidity_array as $key => $level)
        {
        	if($humidity_id_array[$key] == 0)
        	{
        		$add_humidity = PinHumidity::insertGetId([
        		    'pin_id'=>$pin_id,
        		    'humidity'=>$level
        		]);
        	}
        	else
            	$update = PinHumidity::find($humidity_id_array[$key])->update(['humidity'=>$level]);
        }
        $data['id'] = $humidity_id;
        if($update == true)
            return $this->sendResponse($data, 'Humidity Updated successfully');
        else
            return $this->sendError('Error', ['error'=>'Humidity did not updated.']);
    }

    public function delete_humidity(Request $request)
    {
        $id = $request->humidity_id;
        $data = PinHumidity::find($id)->delete();
        $response['id'] = $id;
        if($data == true)
            return $this->sendResponse($response, 'Humidity Deleted successfully');
        else
            return $this->sendError('Error', ['error'=>'Humidity did not deleted.']);
    }
}
