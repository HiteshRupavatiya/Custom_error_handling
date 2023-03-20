<?php

namespace App\Http\Controllers\auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\SendVerificationMail;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Auth;
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
                    'remember_token'           => Str::random(10)
                ]
        );

        return ok('User Registered Successfully', $user);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (Auth::attempt($request->only(['email', 'password']))) {
            $token = $user->createToken('API Token')->plainTextToken;
            return ok('Logged In Successfully', $token);
        }

        return error('Invalid User Credentials');
    }
}
