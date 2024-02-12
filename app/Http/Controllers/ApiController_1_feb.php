<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Fields;
use App\Models\Pin;
use App\Models\PinHumidity;
use Auth;
use Mail;
use App\Mail\VerificationMail;
use Hash;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required:min:8'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['otp'] = rand(100000,999999);
        $user = User::create($input);

        $details = [
            'verification_code' => $input['otp']
        ];
        
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

    public function verify_otp(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email' => 'required|email',
            'verification_code' => 'required',
        ]);

        if($validate->fails()){
            return $this->sendError('Validation Error.', $validate->errors());       
        }

        $ver_code = $request->verification_code;
        $email = $request->email;

        $check = User::where([
            'email' => $email,
            'otp' => $ver_code
        ])->get();
        if(count($check) == 1)
        {
            User::where([
                'email' => $email
            ])->update(['otp'=>null]);

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
                'email'=> 'required'
            ]);

            if($validate->fails()){
                return $this->sendError('Validation Error.', $validate->errors());       
            }

            $password = $request->password;
            $password = Hash::make($password);
            $email = $request->email;

            $data = User::where('email',$email)->update([
                'password' => $password,
                'is_temporary' => '0'
            ]);
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
                'email' => 'required|email'
            ]);

            if($validate->fails()){
                return $this->sendError('Validation Error.', $validate->errors());       
            }
            
            $email = $request->email;
            $check = User::where([
                'email' => $email
            ])->get();
            
            if(count($check) == 1)
            {
                $verification_code = rand(100000,999999);
                $data = User::where('email',$email)->update(['otp'=>$verification_code]);
                
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

    public function track_humidity(Request $request)
    {
        $keyword = $request->search;
        $success['data'] = Fields::join('pins','fields.id','pins.field_id')
                            ->join('pin_humidities','pins.id','pin_humidities.pin_id')
                            ->where('fields.field_code','like','%'.$keyword.'%')
                            ->orWhere('fields.field_name','like','%'.$keyword.'%')
                            ->get();
        
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
        // $field_alias = $request->field_alias;
        // $razon_social = $request->razon_social;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $humidity = $request->humidity;
        $humidity_date = $request->humidity_date;
        
        $humidity_array = explode(",",$humidity[0]);
        $field_data_ids = [];
        $create = Pin::insertGetId([
            'user_id' => $user_id,
            'field_id' => $field_id,
            // 'field_alias' => $field_alias,
            // 'razon_social' => $razon_social,
            'latitude' => $latitude,
            'longitude' => $longitude,
            // 'humidity' => $level,
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
}
