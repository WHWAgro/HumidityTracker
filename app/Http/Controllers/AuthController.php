<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Auth;
use Hash;
use Mail;
use App\Mail\VerificationMail;
use App\Mail\TemporaryUsersMail;
use DB;

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
            if(Auth::user())
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

                return redirect('verification_code')->withSuccess('El código de verificación ha sido enviado a su correo')->with( ['email' => $email,'type' => 'forgot'] );
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
            'email' => 'required|email',
            'password' => 'required'
        ])->validate();

        $email = $request->email;
        $temp_password = $request->password;
        $verification_code = rand(100000,999999);

        $data = DB::table('temporary_users')
                    ->where('email',$email)
                    ->where('password',$temp_password)
                    ->where('is_used',0);

        if($data->count() > 0)
            $data = $data->update(['otp'=>$verification_code]);
        else
            return back()->withErrors(['error' => 'Please enter valid email'])->with(['email'=>$email]);

        if($data)
        {
            $details = [
                'verification_code' => $verification_code
            ];
            
            Mail::to($email)->send(new VerificationMail($details));

            return redirect('verification_code')->withSuccess('El código de verificación ha sido enviado a su correo')->with( ['email' => $email,'type' => 'create'] );
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

        if($type == 'create')
        {
            $check = DB::table('temporary_users')->where('email',$email)->where('is_used','0')->count();
            if($check == 1)
                $data = DB::table('temporary_users')->where('email',$email)->update(['otp'=>$verification_code]);
            else
                return back()->withErrors(['error' => 'Please enter valid email'])->with(['email'=>$email,'type' => $type]);
        }
        if($type == 'forgot')
        {
            $check = User::where('email',$email)->count();
            if($check == 1)
                $data = User::where('email',$email)->update(['otp'=>$verification_code]);
            else    
                return back()->withErrors(['error' => 'Please enter valid email'])->with(['email'=>$email,'type' => $type]);
        }

        $details = [
            'verification_code' => $verification_code
        ];
        
        Mail::to($email)->send(new VerificationMail($details));
        return back()->withSuccess('El código de verificación ha sido enviado a su correo')->with( ['email' => $email,'type' => $type] );
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

            if($type == 'create')
            {
                $data = User::create([
                    'email' => $email,
                    'password' => $password
                ]);

                $update_temporary = DB::table('temporary_users')
                                        ->where('email',$email)
                                        ->update(['is_used' => '1']);

                return redirect('success')->with(['email'=>$email]);
            }
            if($type == 'forgot')
            {
                $data = User::where('email',$email)->update([
                    'password' => $password
                ]);
                // return redirect('track_humidity')->with(['email'=>$email]);
                return redirect('login');
            }

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


    public function send_temp_user_mail()
    {
        $users = DB::table('temporary_users')
                    ->where('is_used','0')
                    ->where('invitation_sent','0')
                    ->get();

        foreach($users as $user)
        {
            $details = [
                'email' => $user->email,
                'password' => $user->password
            ];
            
            Mail::to($user->email)->send(new TemporaryUsersMail($details));

            DB::table('temporary_users')->where('email',$user->email)->update(['invitation_sent'=>'1']);
        }
    }
}
