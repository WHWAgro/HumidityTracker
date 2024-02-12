<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Auth;
use Hash;
use Mail;
use App\Mail\VerificationMail;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
            'privacy_policy_and_terms_&_conditions' => 'required'
        ])->validate();

        $email = $request->email;
        $password = $request->password;
        $password = Hash::make($password);

        $data = $request->only('email','password');
        if(Auth::attempt($data))
            if(Auth::user()->is_temporary == 0)
                return redirect('forecast');
            else
            {
                Auth::logout();
                return redirect('login');
            }
        else
            return back()->withErrors(['error' => 'Credentials did not Matched']);
    }

    public function forgot_password(Request $request)
    {
        if($request->all())
        {
            $validate = Validator::make($request->all(),[
                'email' => 'required|email'
            ])->validate();

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

                return redirect('verification_code')->withSuccess('Verification code has been sent to your mail')->with( ['email' => $email,'type' => 'forgot'] );
            }
            else
                return back()->withErrors(['error' => 'This email is not registered.']);

        }
        return view('forgot-password');
    }

    public function invitation()
    {
        return view('accept-invitation');
    }

    public function accept_invitation(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ])->validate();

        $email = $request->email;
        $temp_password = $request->password;
        $temp_password = Hash::make($temp_password);
        $verification_code = rand(100000,999999);

        $data = User::insertGetId([
            'email' => $email,
            'password' => $temp_password,
            'otp' => $verification_code
        ]);

        if($data)
        {
            
            $details = [
                'verification_code' => $verification_code
            ];
            
            Mail::to($email)->send(new VerificationMail($details));

            return redirect('verification_code')->withSuccess('Verification code has been sent to your mail')->with( ['email' => $email,'type' => 'create'] );
        }
        else
            return back()->withErrors();
    }

    public function verification_code()
    {
        return view('verification-code');
    }

    public function resend_code(Request $request)
    {
        $email = $request->email;
        $type = $request->type;
        $verification_code = rand(100000,999999);
        $data = User::where('email',$email)->update(['otp'=>$verification_code]);
        
        $details = [
            'verification_code' => $verification_code
        ];
        
        Mail::to($email)->send(new VerificationMail($details));
        return back()->withSuccess('Verification code has been sent to your mail')->with( ['email' => $email,'type' => $type] );
    }

    public function verify_otp(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'email' => 'required|email',
            'otp' => 'required'
        ])->validate();

        $otp = $request->otp;
        $ver_code = $otp[0].$otp[1].$otp[2].$otp[3].$otp[4].$otp[5];
        $email = $request->email;
        $type = $request->type;

        $check = User::where([
            'email' => $email,
            'otp' => $ver_code
        ])->get();
        if(count($check) == 1)
        {
            User::where([
                'email' => $email
            ])->update(['otp'=>null]);

            return redirect('create_password')->with( ['email' => $email,'type' => $type] );
        }

        return back()->withErrors(['error' => 'Please enter valid code'])->with(['email'=>$email,'type' => $type]);
    }

    public function create_password(Request $request)
    {
        if($request->all())
        {
            $validate = Validator::make($request->all(),[
                'password' => 'required|min:8',
                'confirm_password' => 'required|same:password'
            ])->validate();

            $password = $request->password;
            $password = Hash::make($password);
            $email = $request->email;
            $type = $request->type;

            $data = User::where('email',$email)->update([
                'password' => $password,
                'is_temporary' => '0'
            ]);

            if($type == 'forgot')
                return redirect('track_humidity')->with(['email'=>$email]);
            if($type == 'create')
                return redirect('success')->with(['email'=>$email]);

            return redirect('login');
        }
        
        return view ('create-password');
    }

    public function success()
    {
        return view('success');
    }

    public function track_humidity(Request $request)
    {
        if($request->email)
        {
            $user_id = User::select('id')->where('email',$request->email)->first();
            Auth::loginUsingId($user_id->id);
            return redirect('forecast');
        }
        return view('track-humidity');
    }

    public function logout(Request $request) 
    {
        Auth::logout();
        return redirect('login');
    }
}
