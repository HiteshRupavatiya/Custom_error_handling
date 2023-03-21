<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Mail\VerifyEmail;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|alpha|min:5|max:30',
            'email'                 => 'required|email|unique:users,email|max:40',
            'password'              => 'required|min:8|max:15',
            'password_confirmation' => 'required|same:password'
        ]);

        $user = User::create(
            $request->only(
                [
                    'name',
                    'email'
                ]
            )
                + [
                    'password'                 => Hash::make($request->password),
                    'remember_token'           => Str::random(10),
                    'email_verification_token' => Str::random(64)
                ]
        );

        Mail::to($user->email)->send(new WelcomeEmail($user));

        Mail::to($user->email)->send(new VerifyEmail($user));

        $token = $user->createToken('API Token')->accessToken;

        return ok('User Registered Successfully', $user);
    }

    public function login(Request $request)
    {
        $user = $request->validate([
            'email'    => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        if (!auth()->attempt($user)) {
            return error('Invalid User Details');
        }

        $token = auth()->user()->createToken('API Token')->accessToken;

        return ok('Logged In Successfully', $token);
    }

    public function verifyEmail($token)
    {
        $user = User::where('email_verification_token', $token)->first();
        if ($user) {
            $user->update([
                'is_active'                => true,
                'email_verified_at'        => now(),
                'email_verification_token' => '',
            ]);

            return ok('Email Verified Successfully');
        } else {
            return error('Email Already Verified');
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email|unique:password_resets,email',
        ]);

        $token = Str::random(64);

        $password_reset = PasswordReset::create([
            'token'      => $token,
            'email'      => $request->email,
            'created_at' => now(),
            'expired_at' => now()->addDays(2)
        ]);

        Mail::to($request->email)->send(new ResetPasswordEmail($password_reset));

        return ok('Password Forgot Mail Sent Successfully');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|exists:users,email|exists:password_resets,email',
            'password'              => 'required|min:8|max:20',
            'password_confirmation' => 'required|same:password',
            'token'                 => 'required|exists:password_resets,token',
        ]);

        $hasData = PasswordReset::where('email', $request->email)->first();

        $hasData->expired_at >= $hasData->created_at;

        if ($hasData) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);

                PasswordReset::where('email', $request->email)->delete();

                return ok('Password Changed Successfully');
            }
        } else {
            return error('Token Has Been expired');
        }
    }
}
